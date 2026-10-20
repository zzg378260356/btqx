<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, defineAsyncComponent } from "vue";
import type { EChartsOption } from "echarts";
import Chart from "./components/Chart.vue";
import DesignAsset from "./components/DesignAsset.vue";
import PanelHeading from "./components/PanelHeading.vue";
import MetricCard from "./components/MetricCard.vue";
import MapLegend from "./components/MapLegend.vue";
import { designData, designTracking, type DashboardData, type Warning } from "./designData";
const Scene = defineAsyncComponent(() => import("./components/Scene.vue"));
const b = "/figma/";
const mode = ref<"design" | "live">("design");
const live = ref<DashboardData | null>(null);
const emptyData: DashboardData = { counts: { red: 0, orange: 0, yellow: 0, blue: 0 }, devices: { total: 0, online: 0 }, warnings: [], trend: { dates: [], series: {} }, types: [] };
const data = computed(() => mode.value === "design" ? designData : live.value || emptyData);
const colors: Record<string, string> = { red: "#ff004c", orange: "#ff9f0a", yellow: "#f1ff00", blue: "#00baff" };
const names: Record<string, string> = { red: "红色", orange: "橙色", yellow: "黄色", blue: "蓝色" };
const keys = Object.keys(names);
const level = ref("");
const district = ref("");
const status = ref("");
const selected = ref<Warning | null>(null);
const station = ref("");
const error = ref("");
const active = ref("综合态势");
const overview = ref(false);
const clock = ref(new Date());
const scale = ref(1);
const offset = ref({ x: 0, y: 0 });
let timer: ReturnType<typeof setInterval>;
let poll: ReturnType<typeof setInterval>;
const tabs = ["综合态势", "三维孪生一张图", "监测设备中心", "检测点详情", "预警中心", "应急处置指挥", "智能模型分析", "数据资源中心", "事态复盘评估"];
const navAssets = ["c68f1bff-9523-4a33-824a-2f5e78d63ffe.svg", "c569da77-d28e-446b-bec7-077efc479a05.svg", "c569da77-d28e-446b-bec7-077efc479a05.svg", "c569da77-d28e-446b-bec7-077efc479a05.svg", "c40c045c-b059-4ec0-b6f7-b8535f218322.svg", "c5c64c41-6baf-467f-9c03-2284289677c3.svg", "f8b34b7d-538e-40b6-8d87-f5c32fa17207.svg", "f8b34b7d-538e-40b6-8d87-f5c32fa17207.svg", "5f1dd80c-8b07-4a74-88e5-4353dab48f91.svg"];
const filtered = computed(() => data.value.warnings.filter(w => (!level.value || w.level === level.value) && (!district.value || w.district === district.value) && (!status.value || w.status === status.value)));
const tracking = computed(() => (mode.value === "design" ? designTracking : data.value.warnings).filter(w => (!level.value || w.level === level.value) && (!district.value || w.district === district.value) && (!status.value || w.status === status.value)));
const total = computed(() => Object.values(data.value.counts).reduce((a, n) => a + n, 0));
const districts = computed(() => [...new Set([...data.value.warnings, ...tracking.value].map(w => w.district))]);
const typeColors = ["#00baff", "#6364ff", "#ee91e6", "#ff1616", "#ffdf00"];
const percentages = computed(() => data.value.types.map((t, i) => mode.value === "design" ? [40, 23, 18, 11, 8][i] : Math.round(t.value / (data.value.types.reduce((s, t) => s + t.value, 0) || 1) * 100)));
const trend = computed<EChartsOption>(() => ({
  color: Object.values(colors), tooltip: { trigger: "axis" },
  legend: { right: 8, top: 0, icon: "circle", itemWidth: 8, itemHeight: 8, textStyle: { color: "#cfe3ec", fontSize: 12 } },
  grid: { left: 42, right: 18, top: 44, bottom: 32 },
  xAxis: { type: "category", data: data.value.trend.dates, axisLabel: { color: "#cfe3ec", fontSize: 12 }, axisLine: { show: false }, axisTick: { show: false } },
  yAxis: { type: "value", min: 0, max: mode.value === "design" ? 50 : undefined, axisLabel: { color: "#cfe3ec" }, splitLine: { lineStyle: { color: "#b3d8f133", type: "dashed" } } },
  series: keys.map(k => ({ name: names[k], type: "line", smooth: false, symbol: "circle", symbolSize: 7, itemStyle: { color: "#cee3f0" }, lineStyle: { color: colors[k], width: 3 }, data: data.value.trend.series[k] ?? [] })),
}));
const pie = computed<EChartsOption>(() => ({
  color: typeColors, tooltip: { trigger: "item" }, animation: false,
  series: [
    { type: "pie", radius: [56, 63], center: [87, 133], startAngle: 96, label: { show: false }, itemStyle: { borderWidth: 2, borderColor: "#102a42" }, data: data.value.types },
    { type: "pie", radius: [69, 76], center: [87, 133], silent: true, label: { show: false }, data: [{ value: 1, itemStyle: { color: "#1b4b7080" } }] },
    { type: "pie", radius: [85, 86], center: [87, 133], silent: true, label: { show: false }, data: [{ value: 1, itemStyle: { color: "#2aa9dd" } }] },
  ],
}));
const pins = [
  { x: 574, y: 298, asset: "c15d8a40-8eab-4a37-8619-734664d565ab.svg", name: "白云鄂博矿区" },
  { x: 810, y: 381, asset: "7c68fa92-a107-4cea-aa9a-d55abc36f7f3.svg", name: "白云鄂博矿区监测点" },
  { x: 678, y: 616, asset: "51186279-360e-4038-83a2-1defd228bf00.svg", name: "土默特右旗监测点" },
  { x: 1151, y: 685, asset: "1cf17eea-f58c-457b-90d7-d69c9afaa2ae.svg", name: "东河区监测点" },
  { x: 901, y: 688, asset: "1cf17eea-f58c-457b-90d7-d69c9afaa2ae.svg", name: "青山区监测点" },
  { x: 1119, y: 415, asset: "1cf17eea-f58c-457b-90d7-d69c9afaa2ae.svg", name: "达尔罕茂明安联合旗", red: true },
];
const channels = [
  { name: "短信", value: "26,830", rate: "98.6" }, { name: "微信", value: "18,450", rate: "97.3" },
  { name: "应急广播", value: "562", rate: "99.1" }, { name: "手机APP", value: "15,720", rate: "98.6" },
  { name: "电子屏", value: "236", rate: "97.3" }, { name: "传真", value: "85", rate: "96.1" },
];
const full = ref(false);
function fit() { scale.value = Math.min(window.innerWidth / 1920, window.innerHeight / 1080); offset.value = { x: (window.innerWidth - 1920 * scale.value) / 2, y: (window.innerHeight - 1080 * scale.value) / 2 }; }
async function load() {
  try {
    const response = await fetch("/api/dashboard");
    if (!response.ok) throw new Error("数据服务暂不可用");
    const result = await response.json();
    if (!result.counts || !Array.isArray(result.warnings) || !result.trend || !Array.isArray(result.types)) throw new Error("数据格式异常");
    live.value = result; error.value = "";
  } catch (e) { error.value = e instanceof Error ? e.message : "数据读取失败"; }
}
function setMode() { mode.value = mode.value === "design" ? "live" : "design"; level.value = district.value = status.value = ""; if (mode.value === "live") load(); }
function navigate(tab: string) {
  if (tab === "监测设备中心") { window.open("/admin/devices", "_blank", "noopener"); return; }
  if (["综合态势", "三维孪生一张图", "预警中心"].includes(tab)) { active.value = tab; if (tab === "预警中心") overview.value = true; return; }
  overview.value = true;
}
async function toggleFullscreen() {
  if (document.fullscreenElement) await document.exitFullscreen(); else await document.documentElement.requestFullscreen();
}
function fullscreenChanged() { full.value = !!document.fullscreenElement; fit(); }
function onKey(event: KeyboardEvent) { if (event.key === "Escape") { selected.value = null; station.value = ""; overview.value = false; } }
onMounted(() => {
  fit(); load(); window.addEventListener("resize", fit); window.addEventListener("keydown", onKey); document.addEventListener("fullscreenchange", fullscreenChanged);
  timer = setInterval(() => { clock.value = new Date(); }, 1000); poll = setInterval(load, 30000);
});
onBeforeUnmount(() => { clearInterval(timer); clearInterval(poll); window.removeEventListener("resize", fit); window.removeEventListener("keydown", onKey); document.removeEventListener("fullscreenchange", fullscreenChanged); });
</script>
<template>
  <div class="viewport">
    <div class="dashboard" :style="{ transform: 'translate(' + offset.x + 'px,' + offset.y + 'px) scale(' + scale + ')' }">
      <img class="park-background" :src="b + '67f75703-9c95-4967-b8f1-605ae1cf207d.png'" alt="" />
      <div class="background-tint"></div>
      <DesignAsset class="background-mask" :src="b + '6378aeda-e93c-4dcd-8733-c4bd21d513a2.svg'" :width="3275.76" :height="2262.13" />
      <header class="topbar">
        <div class="weather">
          <div class="time"><b>{{ mode === 'design' ? '12:23:37' : clock.toLocaleTimeString('zh-CN', { hour12: false }) }}</b><small>{{ mode === 'design' ? '2026-10-08' : clock.toLocaleDateString('sv-SE') }}</small></div>
          <DesignAsset :src="b + 'c84daaf8-691a-4308-bd74-f7223734d628.svg'" :width="1" :height="22" />
          <span class="weather-icon"><DesignAsset class="sun" :src="b + 'b21336bd-7b2a-4a76-bf17-148402c0cf66.svg'" :width="14" :height="14" /><DesignAsset class="cloud" :src="b + '76fd5013-cf54-4632-8ae3-1f73032e4ca1.svg'" :width="31.5" :height="20.2" /></span>
          <span class="weather-text">晴</span><DesignAsset :src="b + 'a2ab1faa-b3e6-407c-9d07-38b9ca9bd540.svg'" :width="26" :height="26" /><span class="weather-text">23°</span>
        </div>
        <div class="title-frame"></div><h1>包头市重点区域气象灾害监测预警平台</h1>
        <div class="header-actions">
          <button class="notification-button" title="查看预警信息" @click="overview = true"><span class="bell"></span><i></i></button>
          <button class="fullscreen-button" :title="full ? '退出全屏' : '全屏显示'" @click="toggleFullscreen"><span></span></button>
          <a class="admin-link" href="/admin" target="_blank" rel="noopener"><img :src="b + '966bd149-7415-4ebd-ae96-59aeb9c9b7e0.png'" alt="" />管理员<DesignAsset :src="b + '01eed408-0692-4304-9454-deb7d058440d.svg'" :width="16" :height="16" /></a>
        </div>
        <img class="top-line" :src="b + '0df04644-ca3d-41cf-a557-fcabad58268d.png'" alt="" />
        <div class="top-light">
          <DesignAsset :src="b + '278947fa-c4db-4221-a562-ecb707f81178.svg'" :width="172.1" :height="22" style="left:140.96px;top:0" />
          <DesignAsset :src="b + 'e9980e41-2553-441b-ad2d-591e9f348d66.svg'" :width="325.93" :height="22" style="left:64.06px;top:0" />
          <DesignAsset :src="b + 'd3a80494-0095-4787-85e5-cad473e7a7f1.svg'" :width="454" :height="5.41" style="left:0;top:8.3px" />
          <DesignAsset :src="b + '821899d2-9152-4690-bd1b-82ae12edeb7f.svg'" :width="187.8" :height="11.71" style="left:133.1px;top:5.15px" />
        </div>
      </header>
      <nav class="navigation" aria-label="平台导航">
        <button v-for="(tab, i) in tabs" :key="tab" :class="{ active: active === tab }" @click="navigate(tab)">
          <DesignAsset class="nav-texture" :src="b + navAssets[i]" :width="48" :height="i === 4 ? 207.147 : 208" :style="{ transform: i > 4 ? 'rotate(90deg) scaleX(-1)' : 'rotate(-90deg)' }" /><span>{{ tab }}</span>
        </button>
      </nav>
      <aside class="left-column side-panel">
        <section class="warning-statistics">
          <PanelHeading title="预警统计" more @more="overview = true" />
          <div class="warning-counts">
            <button v-for="key in keys" :key="key" :style="{ '--level-color': colors[key] }" :class="{ chosen: level === key }" @click="level = level === key ? '' : key" :aria-label="'筛选' + names[key] + '预警'">
              <span class="hex-badge"><span class="siren"><i></i></span></span>
              <strong>{{ data.counts[key] }}<small>条</small></strong><span>{{ names[key] }}预警</span><span>较昨日</span><em>+4</em>
            </button>
          </div>
        </section>
        <section class="trend-panel">
          <PanelHeading title="预警趋势" more @more="overview = true" />
          <div v-if="mode === 'design'" class="design-trend">
            <div class="trend-legend"><span v-for="key in keys" :key="key"><i :style="{ background: colors[key] }"></i>{{ names[key] }}</span></div>
            <div class="trend-grid">
              <div v-for="n in [50, 40, 30, 20, 10, 0]" :key="n"><span>{{ n }}</span><DesignAsset :src="b + '422740cf-21d6-4a40-8976-2c266a0c76b4.svg'" :width="310" :height="1" /></div>
            </div>
            <DesignAsset class="original-lines" :src="b + '22f10204-abd6-4b2d-9e21-3df06a4a9b1a.svg'" :width="310" :height="186" />
            <div class="trend-dates"><span v-for="date in designData.trend.dates" :key="date">{{ date }}</span></div>
          </div>
          <Chart v-else :option="trend" />
        </section>
        <section class="type-panel">
          <PanelHeading title="预警类型占比" />
          <div class="type-chart"><Chart :option="pie" /><div class="pie-total"><strong>{{ total }}</strong><span>总条数</span></div><i v-for="n in 4" :key="n" :class="'ring-tick tick-' + n"></i></div>
          <div class="type-legend"><div v-for="(t, i) in data.types" :key="t.name" :style="{ '--type-color': typeColors[i] }"><span><i></i>{{ t.name }}</span><strong>{{ t.value }}条({{ percentages[i] }}%)</strong></div></div>
        </section>
      </aside>
      <div class="metrics">
        <MetricCard :caption="mode === 'design' ? '影响人数 18,760人' : '当前有效预警'" :value="total" unit="条" label="当前生效预警" />
        <MetricCard :caption="mode === 'design' ? '预计影响人数 6,230人' : '监测设备总数'" :value="mode === 'design' ? 25 : data.devices.total" :unit="mode === 'design' ? '条' : '台'" :label="mode === 'design' ? '即将生效预警' : '监测设备总数'" />
        <MetricCard :caption="mode === 'design' ? '较昨日 +12' : '在线监测设备'" :value="mode === 'design' ? 36 : data.devices.online" :unit="mode === 'design' ? '条' : '台'" :label="mode === 'design' ? '预警解除' : '在线设备'" />
        <MetricCard :caption="mode === 'design' ? '较昨日 +28' : '设备在线率'" :value="mode === 'design' ? 152 : Math.round(data.devices.online / (data.devices.total || 1) * 100)" :unit="mode === 'design' ? '个' : '%'" :label="mode === 'design' ? '预警触发检测点' : '在线率'" />
      </div>
      <div v-if="active !== '三维孪生一张图'" class="map-overlays">
        <button v-for="pin in pins" :key="pin.name" class="map-pin" :style="{ left: pin.x + 'px', top: pin.y + 'px' }" :aria-label="pin.name" @click="station = pin.name">
          <span v-if="pin.red" class="unavailable-red-pin"><i></i></span>
          <DesignAsset v-else :src="b + pin.asset" :width="52.83" :height="102.48" />
        </button>
        <button class="region-label label-orange" @click="station = '白云鄂博矿区'">白云鄂博矿区</button>
        <button class="region-label label-blue" @click="station = '达尔罕茂明安联合旗'">达尔汗茂明安联合旗</button>
      </div>
      <div v-else class="cesium-container"><Scene @select="station = $event" /></div>
      <MapLegend :names="names" @select="key => level = level === key ? '' : key" />
      <section class="tracking-panel">
        <PanelHeading title="预警处置跟踪" :width="1091" />
        <div class="tracking-scroll"><table><thead><tr><th>预警名称</th><th>预警等级</th><th>发布时间</th><th>影响区域</th><th>处置状态</th><th>责任单位</th><th>更新时间</th></tr></thead>
          <tbody><tr v-for="w in tracking" :key="w.id" @click="selected = w" tabindex="0" @keydown.enter="selected = w"><td>{{ w.title }}</td><td><span class="badge" :style="{ '--level-color': colors[w.level] }">{{ names[w.level] }}预警</span></td><td>{{ w.published_at }}</td><td>{{ w.district }}</td><td><span :class="['state', { done: w.status === '已完成' }]">{{ w.status }}</span></td><td>{{ w.responsible_unit }}</td><td>{{ w.updated_at || w.published_at }}</td></tr>
          <tr v-if="!tracking.length"><td colspan="7" class="empty">暂无符合条件的记录</td></tr></tbody>
        </table></div>
      </section>
      <aside class="right-column side-panel">
        <section class="warning-list">
          <PanelHeading title="预警信息列表" :width="388" />
          <div class="filters"><select v-model="status" aria-label="预警状态"><option value="">全部预警</option><option>处置中</option><option>已完成</option></select><select v-model="level" aria-label="预警等级"><option value="">全部等级</option><option v-for="key in keys" :key="key" :value="key">{{ names[key] }}预警</option></select><select v-model="district" aria-label="预警区域"><option value="">全部区域</option><option v-for="d in districts" :key="d">{{ d }}</option></select></div>
          <div class="list-scroll"><button v-for="w in filtered" :key="w.id" class="warning-item" :style="{ '--level-color': colors[w.level] }" @click="selected = w"><span class="warning-icon">{{ names[w.level] }}<br />预警</span><div class="warning-copy"><b>{{ w.title }}</b><small>发布时间：{{ w.published_at }}</small></div><div class="warning-area"><span>{{ w.district }}</span><small>人数：{{ (w.population || 8560).toLocaleString() }} 人</small></div></button><p v-if="!filtered.length" class="empty">暂无符合条件的预警</p></div>
        </section>
        <section class="channels-panel">
          <PanelHeading title="预警发布渠道" />
          <div class="channel-grid"><article v-for="c in channels" :key="c.name"><span><i class="channel-icon"></i>{{ c.name }}</span><strong>{{ c.value }}<small>条</small></strong><small>成功率 {{ c.rate }}%</small></article></div>
        </section>
      </aside>
      <div class="mode-control"><button @click="setMode">{{ mode === 'design' ? '设计对照' : '实时数据' }} ⌄</button><span v-if="mode === 'live' && error">{{ error }}</span><span v-else-if="mode === 'live'">每30秒更新</span></div>
      <div v-if="station" class="station-popup"><button class="close-button" @click="station = ''" aria-label="关闭">×</button><h3>{{ station }}</h3><p>重点区域气象灾害监测点</p><button class="action-button" @click="overview = true; station = ''">查看相关预警</button></div>
      <div v-if="selected || overview" class="modal-backdrop" @click.self="selected = null; overview = false">
        <section class="modal" role="dialog" aria-modal="true" :aria-label="selected ? '预警详情' : '预警中心'"><button class="close-button" @click="selected = null; overview = false" aria-label="关闭">×</button><PanelHeading :title="selected ? '预警详情' : '预警中心'" :width="580" />
          <template v-if="selected"><h3>{{ selected.title }}</h3><p :style="{ color: colors[selected.level] }">{{ names[selected.level] }}预警 · {{ selected.status }}</p><p>影响区域：{{ selected.district }}</p><p>发布时间：{{ selected.published_at }}</p><p>责任单位：{{ selected.responsible_unit }}</p></template>
          <template v-else><p>{{ mode === 'design' ? 'Figma 设计示例数据' : '当前数据服务' }} · 共 {{ filtered.length }} 条预警</p><div class="overview-list"><button v-for="w in filtered" :key="w.id" @click="selected = w"><span :style="{ color: colors[w.level] }">{{ names[w.level] }}</span>　{{ w.title }}<small>{{ w.published_at }}</small></button></div></template>
        </section>
      </div>
    </div>
  </div>
</template>
