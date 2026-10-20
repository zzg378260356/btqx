<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { mockOverviewChartData, monitoringOption, warningOption, type OverviewChartData } from "../overviewCharts";
import OverviewElements from "./OverviewElements.vue";
import { mockOverviewElements, type OverviewElementsData } from "../overviewElements";
import NativeDesign, { type DesignNode } from "./NativeDesign";
import cachedDesign from "../generated/overview-design.json";
import DesignAsset from "./DesignAsset.vue";
import PanelHeading from "./PanelHeading.vue";
import MapLegend from "./MapLegend.vue";
import Chart from "./Chart.vue";
const emit = defineEmits<{ warnings: [] }>();
const props = withDefaults(defineProps<{ deviceOnlineRate?: number; chartData?: OverviewChartData; elementsData?: OverviewElementsData }>(), { deviceOnlineRate: 0 });
const design = cachedDesign as unknown as Record<string, DesignNode>;
const modal = ref("");
const riskFilter = ref("");
const names = { red: "红色", orange: "橙色", yellow: "黄色", blue: "蓝色" };
const elements = computed(() => props.elementsData ?? mockOverviewElements);
const warningTypeTotal = computed(() => elements.value.warningTypes.reduce((sum, item) => sum + item.count, 0));
const broadcastText = computed(() => elements.value.broadcasts.join('　　｜　　'));
const events = [
  { name: "橙色暴雨预警-东河区", time: "07-04 20:15", level: "orange", mark: "◉" },
  { name: "雷电蓝色预警-青山区", time: "07-04 20:15", level: "blue", mark: "ϟ" },
  { name: "大风蓝色预警-昆都仑区", time: "07-04 20:15", level: "blue", mark: "≋" },
  { name: "冰雹橙色预警-石拐区", time: "07-04 20:15", level: "orange", mark: "☁" },
];
const shownEvents = computed(() => riskFilter.value ? events.filter(e => e.level === riskFilter.value) : events);
const resources = [{ value: "532", unit: "人", label: "应急人员" }, { value: "65", unit: "辆", label: "救援车辆" }, { value: "94", unit: "套", label: "物资装备" }, { value: "125", unit: "处", label: "避灾场所" }];
const chartData = computed(() => props.chartData ?? mockOverviewChartData);
const trend = computed(() => warningOption(chartData.value.warnings));
const rainfallChart = computed(() => monitoringOption(chartData.value.monitoring));
const waterChart = computed(() => monitoringOption(chartData.value.monitoring, true));
const chartPanels = ref<HTMLDivElement>();
const plotWidth = ref(517);
let chartPanelObserver: ResizeObserver | undefined;
function close(event: KeyboardEvent) { if (event.key === "Escape") modal.value = ""; }
onMounted(() => {
  window.addEventListener("keydown", close);
  chartPanelObserver = new ResizeObserver(entries => { plotWidth.value = (entries[0]!.contentRect.width - 16) / 2; });
  if (chartPanels.value) chartPanelObserver.observe(chartPanels.value);
});
onBeforeUnmount(() => { window.removeEventListener("keydown", close); chartPanelObserver?.disconnect(); });
</script>
<template>
  <div :class="['overview-screen', { 'hide-native-event': riskFilter && riskFilter !== 'orange' }]" aria-label="综合态势">
    <div class="overview-left-surface"></div><div class="overview-right-surface"></div>
    <div class="overview-native-plane">
      <div class="overview-metric-shade"></div>
      <OverviewElements :data="elements" @details="title => modal = title" />
      <div class="overview-trend-heading"><PanelHeading title="预警趋势" :width="386" /></div>
      <div class="overview-trend"><Chart :option="trend" /></div>
      <div ref="chartPanels" class="overview-chart-panels">
        <section class="overview-live-chart" aria-label="降水-位移图表">
          <PanelHeading title="降水-位移" :width="plotWidth" />
          <Chart :option="rainfallChart" />
        </section>
        <section class="overview-live-chart" aria-label="水位-流量图表">
          <PanelHeading title="水位-流量" :width="plotWidth" />
          <Chart :option="waterChart" />
        </section>
      </div>
      <NativeDesign :tree="design.rightPanels" fluid />
      <NativeDesign :tree="design.redMetric" fluid />
      <NativeDesign :tree="design.blueMetric" fluid />
      <NativeDesign :tree="design.rainMetric" fluid />
      <div class="overview-fallback-metric orange-metric" style="left:617px;top:83px">
        <DesignAsset src="/figma/overview/66833337-4ae5-4c33-b5b9-370ca93316a9-03e85.png" :width="69" :height="76" />
        <span class="metric-face" style="--level-color:#ffbc62"><span class="siren"><i></i></span></span><div><span>橙色预警</span><strong>2<small>处</small></strong></div>
      </div>
      <div class="overview-fallback-metric" style="left:967px;top:88px">
        <DesignAsset src="/figma/overview/66833337-4ae5-4c33-b5b9-370ca93316a9-03e85.png" :width="69" :height="76" />
        <span class="metric-face"><span class="equipment-symbol"><i></i></span></span><div><span>设备在线率</span><strong>{{ deviceOnlineRate }}<small>%</small></strong></div>
      </div>
      <div class="overview-fallback-metric" style="left:1317px;top:83px">
        <DesignAsset src="/figma/overview/66833337-4ae5-4c33-b5b9-370ca93316a9-03e85.png" :width="69" :height="76" />
        <span class="metric-face"><b class="lightning-symbol">ϟ</b></span><div><span>雷暴热点</span><strong>18<small>处</small></strong></div>
      </div>
      <section class="announcement" aria-label="实时预警信息滚动播报">
        <svg class="speaker-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5Z" fill="currentColor" stroke="none"/><path d="M15 8a6 6 0 0 1 0 8m3-11a10 10 0 0 1 0 14"/></svg>
        <h2>实时预警信息滚动播报</h2>
        <div class="broadcast-window" tabindex="0" :aria-label="broadcastText || '暂无预警播报'">
          <div v-if="broadcastText" :key="broadcastText" class="broadcast-track"><span>{{ broadcastText }}</span><span aria-hidden="true">{{ broadcastText }}</span></div>
          <span v-else>暂无预警播报</span>
        </div>
        <button @click="emit('warnings')">查看详情 &gt;</button>
      </section>
      <div class="overview-events">
        <button v-for="e in shownEvents" :key="e.name" :aria-label="e.name" :class="{ 'native-event-button': e === events[0] }" @click="modal = e.name"><span :class="['event-symbol', e.level]">{{ e.mark }}</span><span>{{ e.name }}</span><time>{{ e.time }}</time></button>
        <p v-if="!shownEvents.length" class="empty">暂无该等级预警事件</p>
      </div>
      <div class="overview-resources"><button v-for="r in resources" :key="r.label" @click="modal = r.label"><strong>{{ r.value }}<small>{{ r.unit }}</small></strong><span>{{ r.label }}</span></button></div>
      <div class="overview-observation-heading"><PanelHeading title="短临监测数据" /></div>
      <div class="overview-observations"><NativeDesign :tree="design.observationTable" /></div>
      <div class="overview-observation-footer"><small>更新时间： 07-04 20:20</small><button @click="modal = '短临监测数据'">查看详情</button></div>
    </div>
    <div class="overview-map-layer">
      <MapLegend class="overview-legend" :names="names" title="风险等级" suffix="" @select="key => riskFilter = riskFilter === key ? '' : key" />
    </div>
    <div v-if="modal" class="modal-backdrop" @click.self="modal = ''"><section class="modal" role="dialog" aria-modal="true" :aria-label="modal"><button class="close-button" aria-label="关闭" @click="modal = ''">×</button><PanelHeading :title="modal" :width="580" />
      <template v-if="modal === '灾害风险分类统计'"><p v-for="item in elements.risks" :key="item.name"><i class="risk-dot" :style="{ background: item.color }"></i>{{ item.name }}　{{ item.count }}个监测点</p></template>
      <template v-else-if="modal === '气象要素监测'"><table class="weather-detail"><thead><tr><th>时间</th><th>降水量(mm)</th><th>风速(m/s)</th><th>气温(°C)</th></tr></thead><tbody><tr v-for="item in elements.weather" :key="item.time"><td>{{ item.time }}</td><td>{{ item.rainfall }}</td><td>{{ item.windSpeed }}</td><td>{{ item.temperature }}</td></tr></tbody></table></template>
      <template v-else-if="modal === '预警类型占比'"><p v-for="item in elements.warningTypes" :key="item.name">{{ item.name }}　{{ item.count }}条　{{ warningTypeTotal ? (item.count / warningTypeTotal * 100).toFixed(1) : '0.0' }}%</p></template>
      <template v-else-if="modal === '短临监测数据'"><p>雨量计　126mm　1小时均值 3.42mm　正常</p><p>风速仪　23mm　1小时均值 3.31m/s　正常</p><p>温湿度仪　9.64°C　1小时均值 9.64°C　正常</p><small>更新时间：07-04 20:20</small></template>
      <template v-else-if="resources.some(r => r.label === modal)"><p>{{ resources.find(r => r.label === modal)?.value }}{{ resources.find(r => r.label === modal)?.unit }} · {{ modal }}</p><p>包头市重点区域应急资源</p></template>
      <template v-else><p>{{ modal }}</p><p>发布时间：07-04 20:15</p><button class="action-button" @click="emit('warnings')">进入预警中心</button></template>
    </section></div>
  </div>
</template>
<style>
.overview-screen { position: absolute; inset: 0; pointer-events: none; }
.overview-screen button,.overview-screen [role="button"],.overview-trend { pointer-events: auto; }
.overview-left-surface,.overview-right-surface { position: absolute; top: 164px; height: 900px; border: 1px solid #0084ff4d; border-radius: 6px; background: linear-gradient(#07315a66,#05233c4d); }
.overview-left-surface { left: 26px; width: 386px; }.overview-right-surface { left: 1518px; width: 386px; }
.overview-left-surface,.overview-right-surface { pointer-events: auto; }
.overview-screen .overview-left-surface,.overview-screen .overview-right-surface { background: linear-gradient(#062440de,#041c30d9); backdrop-filter: blur(3px); }
.overview-native-plane [data-name="S-209"] { background-color: #041d30c4 !important; backdrop-filter: blur(2px); }
.overview-metric-shade { position: absolute; left: 436px; top: 74px; width: 1060px; height: 100px; background: linear-gradient(90deg,#05253c88,#062c4355,transparent); border-radius: 5px; pointer-events: none; }
.overview-native-plane { position: absolute; left: 0; top: 90px; width: 1920px; height: 990px; }
.overview-native-plane p { margin: 0; }.overview-native-plane img { pointer-events: none; }
.overview-native-plane [data-name="一级标题样式-03-h209"] > div:last-child { justify-content: flex-start !important; width: max-content !important; left: 25px !important; }
.overview-native-plane [data-name="一级标题样式-03-h209"] > div:last-child p { font-weight: 700; }
.overview-native-plane [data-node-id="3035:12202"],.overview-native-plane [data-node-id="3035:12278"] { width: 346px !important; }
.overview-native-plane button { transition: filter .15s; }.overview-native-plane button:hover { filter: brightness(1.3); }
.overview-risk-heading { position: absolute; left: 26px; top: 72px; }.overview-risk-bars { position: absolute; left: 26px; top: 121px; width: 373px; height: 152px; cursor: pointer; }
.overview-weather-heading { position: absolute; left: 26px; top: 287px; }
.overview-rainfall,.overview-wind,.overview-temperature { position: absolute; top: 336px; height: 170px; width: 119px; }.overview-rainfall { left: 34px; }.overview-wind { left: 160px; }.overview-temperature { left: 286px; }
.overview-trend-heading { position: absolute; left: 26px; top: 515px; }.overview-trend { position: absolute; left: 26px; top: 552px; width: 386px; height: 182px; }.overview-trend .chart { width: 386px; height: 182px; }
.overview-fallback-metric { position: absolute; width: 173px; height: 76px; }.overview-fallback-metric > .design-asset { position: absolute; left: 0; top: 0; }
.metric-face { position: absolute; left: 10px; top: 14px; width: 48px; height: 48px; border: 2px solid #89d9f7; border-radius: 50%; background: radial-gradient(#115888,#3d91b688); box-shadow: inset 0 0 0 3px #155a82, 0 4px 6px #052641; color: #a4ddf7; }
.metric-face .siren { width: 18px; height: 22px; left: 13px; top: 10px; box-shadow: none; }.metric-face .siren:after { width: 23px; left: -4px; }
.orange-metric .metric-face { border-color: #ffb45e; }
.overview-fallback-metric > div { position: absolute; left: 82px; top: 9px; white-space: nowrap; }.overview-fallback-metric > div > span { font-size: 14px; line-height: 24px; }.overview-fallback-metric strong { display: block; color: #e5f2ff; font-size: 24px; line-height: 42px; font-weight: 600; }.overview-fallback-metric strong small { font-size: 18px; font-weight: 400; }
.equipment-symbol { position: absolute; left: 9px; top: 10px; width: 26px; height: 25px; border: 2px solid #a4ddf7; border-radius: 6px; }.equipment-symbol:before,.equipment-symbol:after { content: ""; position: absolute; left: 4px; right: 4px; height: 3px; background: #a4ddf7; }.equipment-symbol:before { top: 5px; }.equipment-symbol:after { bottom: 5px; }.lightning-symbol { display: block; font-size: 45px; text-align: center; line-height: 40px; }
.announcement { position: absolute; left: 436px; top: 176px; width: 1044px; height: 59px; background: linear-gradient(90deg,#1588d080,#1253824d 75%,transparent); border-bottom: 1px solid #73bbeb88; }
.announcement h2 { top: 15px; left: 46px; font-size: 24px; }.announcement p { position: absolute; left: 303px; top: 19px; font-size: 20px; color: #fff; line-height: 25px; }.announcement button { position: absolute; right: 10px; top: 22px; font-size: 14px; color: #fff; }.speaker-symbol { position: absolute; left: 14px; top: 20px; font-size: 18px; color: #fff; font-weight: 700; }
.overview-events { position: absolute; left: 1530px; top: 396px; width: 362px; display: flex; flex-direction: column; gap: 8px; }
.overview-events button { display: flex; align-items: center; gap: 10px; padding: 0 10px; width: 362px; height: 40px; border-bottom: 1px solid #1782d866; border-right: 1px solid #1782d866; border-radius: 4px; background: linear-gradient(90deg,#0f364e26,#0d436626); font-size: 14px; }
.overview-events time { margin-left: auto; }.event-symbol { display: grid; place-items: center; width: 22px; height: 22px; border: 1px solid; border-radius: 50%; font-size: 20px; }.event-symbol.orange { color: #ffab60; }.event-symbol.blue { color: #8ed6ff; }
.overview-events .native-event-button { opacity: 0; }
.hide-native-event [data-node-id="3035:12379"] { visibility: hidden; }
.overview-resources { position: absolute; left: 1534px; top: 653px; display: flex; gap: 0; }.overview-resources button { width: 88px; height: 65px; display: flex; flex-direction: column; justify-content: center; gap: 5px; padding-left: 12px; text-align: left; background: linear-gradient(110deg,#15588799,#0b315166); border: 1px solid #087aa9; border-radius: 12px 10px 10px 12px; }.overview-resources button strong { font-size: 20px; color: #c7e6ff; }.overview-resources button strong small { font-size: 12px; font-weight: 400; }.overview-resources button > span { font-size: 14px; color: white; }
.overview-observation-heading { position: absolute; left: 1527px; top: 733px; }.overview-observations { position: absolute; left: 1540px; top: 777px; width: 335px; height: 158.794px; }.overview-observation-footer { position: absolute; left: 1540px; top: 917px; width: 335px; display: flex; height: 34px; justify-content: space-between; align-items: center; }.overview-observation-footer small { font-size: 12px; }.overview-observation-footer button { width: 113px; height: 34px; border: 1px solid #0084ff4d; background: #0c3c5a99; border-radius: 3px; font-size: 14px; color: #fff; }
.overview-bottom-legend { position: absolute; left: 751px; top: 781px; height: 15px; display: flex; gap: 14px; font-size: 12px; line-height: 15px; }.overview-bottom-legend span { display: flex; align-items: center; gap: 4px; color: #cfe3ec; }.overview-bottom-legend i { display: inline-block; width: 20px; height: 3px; background: #339cf3; border-radius: 2px; }.overview-bottom-legend span:last-child i { background: #00b9b7; }
.overview-map-layer { position: absolute; inset: 0; pointer-events: none; }.overview-map-layer button { pointer-events: auto; }.overview-screen .overview-legend { top: 339px; pointer-events: auto; }.overview-map-compass.design-asset { position: absolute; left: 1417px; top: 732px; }
.overview-screen .modal-backdrop { pointer-events: auto; }
.overview-chart-panels { position: absolute; left: 440px; right: 430px; top: 733px; height: 241px; display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 16px; }
.overview-live-chart { position: relative; min-width: 0; border: 1px solid #0084ff4d; border-radius: 6px; background: linear-gradient(#062440de,#041c30d9); pointer-events: auto; }
.overview-live-chart .chart { position: absolute; top: 42px; bottom: 0; width: 100%; }
.announcement { display: flex; align-items: center; gap: 14px; padding: 0 12px; pointer-events: auto; }
.announcement .speaker-icon { flex: 0 0 24px; width: 24px; height: 24px; color: #e6f6ff; }
.announcement h2 { position: static; flex: 0 0 auto; font-size: 22px; white-space: nowrap; }
.announcement button { position: static; flex: 0 0 auto; }
.broadcast-window { flex: 1; min-width: 0; overflow: hidden; color: #fff; font-size: 18px; white-space: nowrap; }
.broadcast-track { display: flex; width: max-content; animation: warning-scroll 65s linear infinite; }
.broadcast-track > span { flex-shrink: 0; padding-right: 64px; }
.announcement:hover .broadcast-track,.announcement:focus-within .broadcast-track { animation-play-state: paused; }
@keyframes warning-scroll { to { transform: translateX(-50%); } }
@media(prefers-reduced-motion: reduce) { .broadcast-track { animation: none; } }
.weather-detail { width: 100%; border-collapse: collapse; font-size: 14px; }.weather-detail th,.weather-detail td { padding: 10px 6px; text-align: left; border-bottom: 1px solid #2c668455; }.weather-detail th { color: #87d6ff; }
.risk-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 10px; }

</style>
