<script setup lang="ts">
import { computed } from 'vue';
import type { EChartsOption } from 'echarts';
import Chart from './Chart.vue';
import PanelHeading from './PanelHeading.vue';
import type { OverviewElementsData } from '../overviewElements';
const props = defineProps<{ data: OverviewElementsData }>();
defineEmits<{ details: [title: string] }>();
const totalRisk = computed(() => props.data.risks.reduce((n, r) => n + r.count, 0));
const totalWarnings = computed(() => props.data.warningTypes.reduce((n, r) => n + r.count, 0));
const latest = computed(() => props.data.weather.at(-1));
const windBearing = computed(() => {
  const directions: Record<string, number> = { 北风: 0, 东北风: 45, 东风: 90, 东南风: 135, 南风: 180, 西南风: 225, 西风: 270, 西北风: 315 };
  const degrees = props.data.windDirectionDegrees ?? directions[props.data.windDirection] ?? 0;
  return ((degrees % 360) + 360) % 360;
});
const text = { color: '#cfe3ec', fontSize: 12 };
const tooltip = { trigger: 'axis' as const, appendToBody: true, backgroundColor: '#052338f5', borderColor: '#319dca', textStyle: text };
const riskOption = computed<EChartsOption>(() => ({
  animation: false, grid: { left: 78, right: 44, top: 5, bottom: 5 },
  tooltip: { ...tooltip, valueFormatter: value => `${value} 个监测点` },
  xAxis: { type: 'value', show: false, min: 0, max: Math.max(1, ...props.data.risks.map(r => r.count)) * 1.2 },
  yAxis: { type: 'category', inverse: true, data: props.data.risks.map(r => r.name), axisLabel: text, axisTick: { show: false }, axisLine: { show: false } },
  series: [{ type: 'bar', barWidth: 7, showBackground: true, backgroundStyle: { color: '#36627a33' },
    label: { show: true, position: 'right', color: '#cfe3ec', formatter: p => `${p.value}处` },
    data: props.data.risks.map(r => ({ value: r.count, itemStyle: { color: r.color, borderRadius: 3 } })),
  }],
}));
const pieOption = computed<EChartsOption>(() => ({
  animation: false, color: ['#00baff', '#6464ff', '#ee91e6', '#ff4168', '#ffdf00'],
  tooltip: { ...tooltip, trigger: 'item', formatter: '{b}<br/>{c} 条 · {d}%' },
  legend: { orient: 'vertical', right: 6, top: 'middle', itemWidth: 8, itemHeight: 8, itemGap: 16, textStyle: text,
    formatter: name => { const count = props.data.warningTypes.find(r => r.name === name)?.count ?? 0; return `${name}  ${count}条 (${totalWarnings.value ? Math.round(count / totalWarnings.value * 100) : 0}%)`; } },
  title: { text: String(totalWarnings.value), subtext: '预警总数 / 条', left: '22%', top: '36%', textAlign: 'center', textStyle: { color: '#e6f3ff', fontSize: 30 }, subtextStyle: { color: '#a5cbdf', fontSize: 11 } },
  series: [{ type: 'pie', radius: [60, 75], center: ['23%', '50%'], label: { show: false }, emphasis: { scaleSize: 4 }, itemStyle: { borderColor: '#0c2b44', borderWidth: 2 }, data: props.data.warningTypes.map(r => ({ name: r.name, value: r.count })) }],
}));
const weatherCards = computed(() => [
  { key: 'rainfall' as const, title: '降水量', unit: 'mm', value: props.data.weather.reduce((sum, r) => sum + r.rainfall, 0).toFixed(1), detail: '今日累计', color: '#39b7ff' },
  { key: 'windSpeed' as const, title: '风向风速', unit: 'm/s', value: latest.value?.windSpeed.toFixed(1) ?? '—', detail: props.data.windDirection, color: '#3fdfd3' },
  { key: 'temperature' as const, title: '气温', unit: '°C', value: latest.value?.temperature.toFixed(1) ?? '—', detail: '最新观测', color: '#73ceff' },
]);
function weatherOption(key: 'rainfall' | 'windSpeed' | 'temperature', color: string): EChartsOption {
  const unit = key === 'rainfall' ? 'mm' : key === 'windSpeed' ? 'm/s' : '°C';
  return {
    animation: false, color: [color], tooltip: { ...tooltip, valueFormatter: value => `${value} ${unit}` },
    grid: { left: 24, right: 5, top: 6, bottom: 20 },
    xAxis: { type: 'category', data: props.data.weather.map(r => r.time), axisLabel: { color: '#a9cbde', fontSize: 9, formatter: (v: string) => v.slice(11, 16) }, axisTick: { show: false }, axisLine: { show: false } },
    yAxis: { type: 'value', axisLabel: { color: '#a9cbde', fontSize: 9 }, splitNumber: 2, splitLine: { lineStyle: { color: '#6b9fc522', type: 'dashed' } } },
    series: [{ name: key === 'rainfall' ? '小时降水量' : key === 'windSpeed' ? '风速' : '气温', type: key === 'rainfall' ? 'bar' : 'line', barMaxWidth: 7, symbolSize: 4, data: props.data.weather.map(r => r[key]), lineStyle: { width: 2 } }],
  };
}
</script>
<template>
  <div class="overview-elements">
    <section class="risk-module" aria-label="灾害风险分类统计">
      <PanelHeading title="灾害风险分类统计" :width="386" more @more="$emit('details', '灾害风险分类统计')" />
      <small class="element-source">{{ totalRisk }}个监测点</small>
      <Chart :option="riskOption" />
    </section>
    <section class="weather-module" aria-label="气象要素监测">
      <PanelHeading title="气象要素监测" :width="386" more @more="$emit('details', '气象要素监测')" />
      <div class="weather-cards">
        <div v-for="card in weatherCards" :key="card.key" class="weather-card" :aria-label="card.title">
          <span>{{ card.title }}</span><strong :style="{ color: card.color }">{{ card.value }}<small>{{ card.unit }}</small></strong><small>{{ card.detail }}</small>
          <svg v-if="card.key === 'windSpeed'" class="wind-compass" viewBox="0 0 100 100" role="img" :aria-label="`${data.windDirection} ${windBearing}度，风速${card.value}米每秒`">
            <title>{{ data.windDirection }} {{ windBearing }}° · {{ card.value }} m/s</title>
            <circle cx="50" cy="50" r="45" fill="#0b3b6440" stroke="#287ce8" stroke-width="1.2" />
            <circle cx="50" cy="50" r="39" fill="none" stroke="#45a3ed" stroke-dasharray="1 3" />
            <g stroke="#3388c8" stroke-width="1"><line v-for="tick in 24" :key="tick" x1="50" y1="9" x2="50" :y2="tick % 6 === 0 ? 17 : 12" :transform="`rotate(${(tick - 1) * 15} 50 50)`" /></g>
            <g class="compass-labels"><text x="50" y="13">N</text><text x="90" y="54">E</text><text x="50" y="96">S</text><text x="10" y="54">W</text></g>
            <g class="compass-needle" :transform="`rotate(${windBearing} 50 50)`"><path d="M50 20 43 50 50 55 57 50Z" fill="#ff5365" /><path d="M50 80 43 50 50 45 57 50Z" fill="#3289ff" /><circle cx="50" cy="50" r="3.5" fill="#eefaff" /></g>
          </svg>
          <Chart v-else :option="weatherOption(card.key, card.color)" />
        </div>
      </div>
    </section>
    <section class="type-module" aria-label="预警类型占比">
      <PanelHeading title="预警类型占比" :width="386" more @more="$emit('details', '预警类型占比')" />
      <Chart :option="pieOption" />
    </section>
  </div>
</template>
<style scoped>
.overview-elements { position: absolute; inset: 0; pointer-events: none; }
.overview-elements section { position: absolute; left: 26px; width: 386px; pointer-events: auto; }
.risk-module { top: 72px; height: 201px; }.risk-module > .chart { position: absolute; top: 54px; width: 100%; height: 147px; }
.element-source { position: absolute; top: 43px; right: 12px; font-size: 10px; color: #96bdd3; }
.weather-module { top: 287px; height: 219px; }.weather-cards { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 6px; padding: 7px 6px 0; }
.weather-card { height: 170px; padding: 5px 3px; background: #0a3a5777; display: flex; flex-direction: column; gap: 4px; font-size: 13px; }.weather-card strong { font-size: 17px; }.weather-card small { font-size: 11px; color: #a9cbde; }.weather-card strong small { margin-left: 2px; color: inherit; }.weather-card .chart { height: 99px; width: 100%; }
.type-module { top: 741px; height: 233px; }.type-module > .chart { position: absolute; top: 42px; width: 100%; bottom: 0; }
.wind-compass { width: 100%; height: 99px; flex-shrink: 0; }
.compass-labels { fill: #d4eaff; font-size: 11px; text-anchor: middle; font-family: sans-serif; }
.compass-needle { transition: transform .4s ease; }
</style>
