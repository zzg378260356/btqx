<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref } from "vue";
import {
  Viewer, Cartesian3, Cartesian2, Color, EllipsoidTerrainProvider, CustomDataSource, DistanceDisplayCondition, ConstantPositionProperty, ConstantProperty,
  LabelStyle, VerticalOrigin, HorizontalOrigin, SceneMode, UrlTemplateImageryProvider,
  WebMercatorTilingScheme, ScreenSpaceEventHandler, ScreenSpaceEventType, Entity, Rectangle,
} from "cesium";
import "cesium/Build/Cesium/Widgets/widgets.css";
interface MapDevice { id: number; code: string; name: string; type: string; status: string; longitude: number; latitude: number; }
interface MapPoint { id: number; code: string; name: string; hazard_type: string; longitude: number; latitude: number; notes?: string; devices: MapDevice[]; }
interface MapData { center: { longitude: number; latitude: number }; tianditu_key?: string; points: MapPoint[]; summary: { points: number; devices: number; online: number }; }
const emit = defineEmits<{ stats: [summary: { points: number; devices: number; online: number }] }>();
const container = ref<HTMLDivElement>();
const points = ref<MapPoint[]>([]);
const summary = ref({ points: 0, devices: 0, online: 0 });
const selected = ref<MapPoint[]>([]);
const deviceType = ref("");
const listing = ref(false);
const layer = ref<"img" | "vec">("img");
const loading = ref(true);
const error = ref("");
const failed = ref(false);
const pendingTiles = ref(0);
const typeNames = computed(() => [...new Set(points.value.flatMap(p => p.devices.map(d => d.type)))]);
const visiblePoints = computed(() => deviceType.value ? points.value.filter(p => p.devices.some(d => d.type === deviceType.value)) : points.value);
let viewer: Viewer | undefined;
let source: CustomDataSource | undefined;
let handler: ScreenSpaceEventHandler | undefined;
let poll: ReturnType<typeof setInterval> | undefined;
let config: MapData | undefined;
let disposed = false;
const controller = new AbortController();
const statusName = (status: string) => ({ online: "在线", offline: "离线", maintenance: "维护中" }[status] || status);
function baotou() {
  const center = config?.center || { longitude: 109.8403, latitude: 40.6574 };
  viewer?.camera.setView({ destination: Cartesian3.fromDegrees(center.longitude, center.latitude, 110000) });
  selected.value = [];
}
function showAll() {
  if (!viewer || !points.value.length) return;
  selected.value = [];
  viewer.camera.setView({ destination: Rectangle.fromDegrees(
    Math.min(...points.value.map(p => p.longitude)) - .04, Math.min(...points.value.map(p => p.latitude)) - .04,
    Math.max(...points.value.map(p => p.longitude)) + .04, Math.max(...points.value.map(p => p.latitude)) + .04,
  ) });
}
function focusPoint(point: MapPoint) {
  selected.value = [point]; listing.value = false;
  viewer?.camera.setView({ destination: Cartesian3.fromDegrees(point.longitude, point.latitude, 6000) });
}
function setLayers() {
  if (!viewer || !config?.tianditu_key) return;
  viewer.imageryLayers.removeAll();
  const key = encodeURIComponent(config.tianditu_key);
  for (const name of [layer.value, layer.value === "img" ? "cia" : "cva"]) {
    const provider = new UrlTemplateImageryProvider({
      url: `https://t{s}.tianditu.gov.cn/DataServer?T=${name}_w&x={x}&y={y}&l={z}&tk=${key}`,
      subdomains: ["0", "1", "2", "3", "4", "5", "6", "7"],
      minimumLevel: 1, maximumLevel: 18, tilingScheme: new WebMercatorTilingScheme(),
      credit: "天地图 · 国家地理信息公共服务平台",
    });
    provider.errorEvent.addEventListener(() => { error.value = "天地图图层暂未加载成功，请检查网络或 Key 的服务权限"; });
    const imagery = viewer.imageryLayers.addImageryProvider(provider);
    imagery.brightness = name === layer.value ? .82 : .9;
  }
  viewer.scene.requestRender();
}
function toggleLayer() { layer.value = layer.value === "img" ? "vec" : "img"; error.value = ""; setLayers(); }
function drawPoints() {
  if (!source || !viewer) return;
  const visibleIds = new Set(visiblePoints.value.map(point => 'monitoring-point-' + point.id));
  source.entities.suspendEvents();
  try {
  for (const entity of [...source.entities.values]) {
    if (!visibleIds.has(entity.id)) source.entities.remove(entity);
  }
  visiblePoints.value.forEach(point => {
    const devices = deviceType.value ? point.devices.filter(d => d.type === deviceType.value) : point.devices;
    const id = 'monitoring-point-' + point.id;
    const color = Color.fromCssColorString(devices.some(d => d.status === "online") ? "#38dfa6" : "#43cfff");
    const label = point.code + ' · ' + devices.length + '台';
    const existing = source!.entities.getById(id);
    if (existing) {
      const position = Cartesian3.fromDegrees(point.longitude, point.latitude);
      if (!Cartesian3.equals(existing.position?.getValue(viewer!.clock.currentTime), position)) existing.position = new ConstantPositionProperty(position);
      if (!Color.equals(existing.point?.color?.getValue(viewer!.clock.currentTime), color)) existing.point!.color = new ConstantProperty(color);
      if (existing.label?.text?.getValue() !== label) existing.label!.text = new ConstantProperty(label);
      return;
    }
    source!.entities.add({
      id,
      name: point.name,
      properties: { pointId: point.id, deviceCount: devices.length },
      position: Cartesian3.fromDegrees(point.longitude, point.latitude),
      point: { pixelSize: 10, color, outlineColor: Color.fromCssColorString("#06233a"), outlineWidth: 2, disableDepthTestDistance: Number.POSITIVE_INFINITY },
      label: { text: label, font: '13px sans-serif', fillColor: Color.WHITE, style: LabelStyle.FILL_AND_OUTLINE, outlineColor: Color.fromCssColorString("#052237"), outlineWidth: 3, pixelOffset: new Cartesian2(0, -12), verticalOrigin: VerticalOrigin.BOTTOM, horizontalOrigin: HorizontalOrigin.CENTER, disableDepthTestDistance: Number.POSITIVE_INFINITY, distanceDisplayCondition: new DistanceDisplayCondition(0, 45000) },
    });
  });
  } finally { source.entities.resumeEvents(); }
  viewer.scene.requestRender();
}
async function refresh(): Promise<void> {
  const response = await fetch("/api/monitoring-map", { signal: controller.signal });
  if (!response.ok) throw new Error("监测点数据暂不可用");
  const data: MapData = await response.json();
  if (!Array.isArray(data.points) || !data.center) throw new Error("监测点数据格式异常");
  if (disposed) return;
  config = data; points.value = data.points; summary.value = data.summary;
  emit("stats", data.summary);
  selected.value = selected.value.map(old => data.points.find(p => p.id === old.id)).filter((p): p is MapPoint => !!p);
  drawPoints();
}
function escape(event: KeyboardEvent) { if (event.key === "Escape") { selected.value = []; listing.value = false; } }
onMounted(async () => {
  window.addEventListener("keydown", escape);
  try {
    await refresh();
    if (disposed) return;
    if (!config?.tianditu_key) throw new Error("尚未配置天地图 Key");
    viewer = new Viewer(container.value!, {
      sceneMode: SceneMode.SCENE2D, baseLayer: false, terrainProvider: new EllipsoidTerrainProvider(),
      animation: false, timeline: false, baseLayerPicker: false, geocoder: false, homeButton: false,
      sceneModePicker: false, navigationHelpButton: false, fullscreenButton: false, infoBox: false,
      selectionIndicator: false, showRenderLoopErrors: false, requestRenderMode: true,
      maximumRenderTimeChange: Number.POSITIVE_INFINITY, targetFrameRate: 30,
    });
    viewer.scene.globe.baseColor = Color.fromCssColorString("#0b273c");
    viewer.scene.backgroundColor = Color.fromCssColorString("#071a2c");
    viewer.scene.globe.enableLighting = false;
    viewer.scene.globe.tileLoadProgressEvent.addEventListener((count: number) => { pendingTiles.value = count; });
    source = new CustomDataSource("installation-monitoring-points");
    await viewer.dataSources.add(source);
    if (disposed) return;
    source.clustering.enabled = false;
    drawPoints(); setLayers(); showAll();
    handler = new ScreenSpaceEventHandler(viewer.scene.canvas);
    handler.setInputAction((click: { position: Cartesian2 }) => {
      const picked = viewer!.scene.pick(click.position) as { id?: Entity | Entity[]; primitive?: { id?: Entity | Entity[] } } | undefined;
      const entity = picked?.id || picked?.primitive?.id;
      if (!entity) { selected.value = []; return; }
      const entities = Array.isArray(entity) ? entity : [entity];
      const ids = entities.map(e => e.properties?.pointId?.getValue());
      selected.value = points.value.filter(p => ids.includes(p.id));
      listing.value = false;
    }, ScreenSpaceEventType.LEFT_CLICK);
    poll = setInterval(() => { refresh().catch(() => { error.value = "监测点刷新失败，保留当前数据"; }); }, 60000);
  } catch (e) {
    if (disposed) return;
    error.value = e instanceof Error && !e.message.includes("WebGL") ? e.message : "当前浏览器无法启动地图，请启用 WebGL";
    failed.value = true;
  } finally {
    loading.value = false;
  }
});
onBeforeUnmount(() => { disposed = true; controller.abort(); if (poll) clearInterval(poll); handler?.destroy(); if (viewer && !viewer.isDestroyed()) viewer.destroy(); window.removeEventListener("keydown", escape); });
</script>
<template>
  <div class="monitoring-map" :data-loading-tiles="pendingTiles">
    <div ref="container" class="map-canvas"></div>
    <div v-if="loading" class="map-message">正在加载天地图与监测点…</div>
    <div v-else-if="error" class="map-message error">{{ error }}</div>
    <div v-if="listing || selected.length" class="map-point-popup">
      <button class="map-popup-close" aria-label="关闭监测点详情" @click="listing = false; selected = []">×</button>
      <h3>{{ listing ? '监测点列表' : selected.length > 1 ? '重合监测点' : selected[0]?.name }}</h3>
      <div v-if="listing || selected.length > 1" class="map-point-list"><button v-for="point in listing ? visiblePoints : selected" :key="point.id" @click="focusPoint(point)"><b>{{ point.code }} · {{ point.name }}</b><small>{{ point.devices.length }} 台设备 · {{ point.hazard_type }}</small></button></div>
      <template v-else-if="selected[0]">
        <p>{{ selected[0].code }} · {{ selected[0].hazard_type }} · {{ selected[0].devices.length }} 台设备</p>
        <p class="point-coordinate">经度 {{ selected[0].longitude }}　纬度 {{ selected[0].latitude }}</p>
        <ul class="map-device-list"><li v-for="device in selected[0].devices" :key="device.id"><div><b>{{ device.type }}</b><small>{{ device.code }}</small></div><span :class="{ online: device.status === 'online' }">{{ statusName(device.status) }}</span></li></ul>
        <p v-if="!selected[0].devices.length">该点暂不安装设备。</p>
        <p v-if="selected[0].notes" class="point-notes">{{ selected[0].notes }}</p>
      </template>
    </div>
  </div>
</template>
<style scoped>
.monitoring-map,.map-canvas { position: absolute; inset: 0; overflow: hidden; }
.monitoring-map { background: #0b273c; }
.map-message { position: absolute; top: 29%; left: 50%; transform: translateX(-50%); background: #052338e8; color: #c7eaff; border: 1px solid #2981a4; padding: 10px 16px; border-radius: 5px; font-size: 14px; }.map-message.error { color: #ffcc9b; }
.map-point-popup { position: absolute; right: calc(22vw + 14px); top: 29vh; width: 360px; max-width: 85vw; max-height: 53vh; overflow: auto; background: #052338f5; color: #cde8ff; border: 1px solid #42bddc; border-radius: 6px; box-shadow: 0 5px 25px #0008; padding: 17px; z-index: 3; }
.map-point-popup h3 { font-size: 17px; padding-right: 17px; margin: 0 0 12px; color: #fff; line-height: 1.5; }.map-popup-close { position: absolute; right: 10px; top: 5px; font-size: 24px; }.map-point-popup p { font-size: 12px; line-height: 1.6; }.point-coordinate,.point-notes { color: #8db4cc; }
.map-point-list { display: flex; flex-direction: column; }.map-point-list button { text-align: left; border-bottom: 1px solid #24546c; padding: 10px 0; }.map-point-list b { display: block; font-size: 13px; font-weight: 500; }.map-point-list small { display: block; font-size: 11px; color: #8db4cc; margin-top: 5px; }
.map-device-list { list-style: none; padding: 0; margin: 10px 0; }.map-device-list li { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #24546c; }.map-device-list b { font-size: 14px; font-weight: 500; }.map-device-list small { display: block; font-size: 10px; color: #87b4cd; margin-top: 4px; }.map-device-list li > span { font-size: 11px; color: #9daebb; }.map-device-list .online { color: #45e8aa; }
:deep(.cesium-viewer-bottom) { right: 5px; left: auto; }
@media(max-width:900px) { .map-point-popup { right: 24%; top: 25%; width: 320px; } }
</style>
