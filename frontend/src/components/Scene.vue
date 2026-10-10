<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref } from "vue";
import {
  Viewer,
  Cartesian3,
  Color,
  Math as CesiumMath,
  EllipsoidTerrainProvider,
  LabelStyle,
  VerticalOrigin,
  Cartesian2,
} from "cesium";
import "cesium/Build/Cesium/Widgets/widgets.css";
const emit = defineEmits<{ select: [name: string] }>();
const container = ref<HTMLDivElement>();
const failed = ref(false);
let viewer: Viewer | undefined;
onMounted(() => {
  try {
    const probe = document.createElement("canvas");
    if (!probe.getContext("webgl2")) {
      failed.value = true;
      return;
    }
    viewer = new Viewer(container.value!, {
      showRenderLoopErrors: false,
      baseLayer: false,
      terrainProvider: new EllipsoidTerrainProvider(),
      animation: false,
      timeline: false,
      baseLayerPicker: false,
      geocoder: false,
      homeButton: false,
      sceneModePicker: false,
      navigationHelpButton: false,
      fullscreenButton: false,
      infoBox: false,
      selectionIndicator: false,
      requestRenderMode: true,
    });
    viewer.scene.backgroundColor = Color.fromCssColorString("#07192e");
    viewer.scene.globe.baseColor = Color.fromCssColorString("#143653");
    viewer.scene.skyAtmosphere!.show = false;
    // Offline demonstration geometry. Replace with surveyed buildings/3D Tiles for production.
    for (let i = 0; i < 9; i++)
      for (let j = 0; j < 6; j++) {
        const height = 25 + ((i * 17 + j * 11) % 6) * 13;
        viewer.entities.add({
          position: Cartesian3.fromDegrees(
            109.825 + i * 0.0012,
            40.65 + j * 0.0012,
            height / 2,
          ),
          box: {
            dimensions: new Cartesian3(65, 75, height),
            material: Color.fromCssColorString(
              (i + j) % 3 === 0 ? "#236b83" : "#193e61",
            ).withAlpha(0.9),
            outline: true,
            outlineColor: Color.fromCssColorString("#3985a6"),
          },
        });
      }
    const points = [
      { name: "白云鄂博监测站", lon: 109.828, lat: 40.652, color: "#ffcf56" },
      {
        name: "达尔罕茂明安监测站",
        lon: 109.832,
        lat: 40.655,
        color: "#ff5574",
      },
      { name: "东河区气象站", lon: 109.826, lat: 40.656, color: "#37d7ff" },
    ];
    points.forEach((p) =>
      viewer!.entities.add({
        name: p.name,
        position: Cartesian3.fromDegrees(p.lon, p.lat, 160),
        cylinder: {
          length: 220,
          topRadius: 12,
          bottomRadius: 12,
          material: Color.fromCssColorString(p.color).withAlpha(0.5),
        },
        label: {
          text: p.name,
          font: "14px sans-serif",
          fillColor: Color.WHITE,
          style: LabelStyle.FILL_AND_OUTLINE,
          outlineWidth: 3,
          outlineColor: Color.fromCssColorString("#06172c"),
          verticalOrigin: VerticalOrigin.BOTTOM,
          pixelOffset: new Cartesian2(0, -20),
        },
      }),
    );
    viewer.camera.setView({
      destination: Cartesian3.fromDegrees(109.831, 40.637, 1600),
      orientation: { heading: 0, pitch: CesiumMath.toRadians(-48), roll: 0 },
    });
    viewer.selectedEntityChanged.addEventListener((entity) => {
      if (entity?.name) emit("select", entity.name);
    });
  } catch {
    failed.value = true;
    container.value?.replaceChildren();
  }
});
onBeforeUnmount(() => viewer?.destroy());
</script>
<template>
  <div class="scene" ref="container"></div>
  <div v-if="failed" class="scene-fallback">
    <div class="fallback-grid">
      <div
        v-for="n in 24"
        :key="n"
        class="fallback-building"
        :style="{ height: 25 + (n % 5) * 10 + 'px' }"
      ></div>
    </div>
    <div class="fallback-pins">
      <button @click="emit('select', '白云鄂博监测站')">◉ 白云鄂博监测站</button
      ><button @click="emit('select', '东河区气象站')">◉ 东河区气象站</button>
    </div>
    <p>
      当前浏览器未提供 WebGL，显示示意图<br /><small
        >启用硬件加速后可查看 Cesium 三维场景</small
      >
    </p>
  </div>
  <div class="scene-note">CESIUM 三维场景 · 演示建筑 / 非实测模型</div>
</template>
