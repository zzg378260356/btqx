import type { EChartsOption } from 'echarts';

export interface MonitoringSample {
  time: string;
  rainfall: number;
  displacement: number;
  waterLevel: number;
  flow: number;
}
export interface WarningSample { time: string; red: number; orange: number; yellow: number; blue: number; }
export interface OverviewChartData { monitoring: MonitoringSample[]; warnings: WarningSample[]; }

// API adapters can supply the same timestamped records without changing the charts.
export const mockOverviewChartData: OverviewChartData = {
  monitoring: Array.from({ length: 12 }, (_, i) => ({
    time: `2026-10-20 ${String(i * 2).padStart(2, '0')}:00`,
    rainfall: [2, 4, 7, 12, 18, 25, 31, 22, 16, 10, 6, 3][i]!,
    displacement: [1.2, 1.3, 1.4, 1.5, 1.9, 2.6, 3.4, 3.8, 4.1, 4.3, 4.4, 4.5][i]!,
    waterLevel: [1.82, 1.8, 1.85, 1.94, 2.12, 2.38, 2.64, 2.71, 2.6, 2.44, 2.29, 2.18][i]!,
    flow: [12, 11, 13, 17, 26, 38, 54, 61, 52, 41, 32, 25][i]!,
  })),
  warnings: Array.from({ length: 7 }, (_, i) => ({
    time: `2026-10-${14 + i}`,
    red: [1, 0, 2, 1, 3, 2, 2][i]!, orange: [3, 2, 4, 3, 5, 4, 3][i]!,
    yellow: [5, 4, 6, 5, 8, 7, 6][i]!, blue: [8, 6, 9, 7, 12, 10, 9][i]!,
  })),
};

const axisLabel = { color: '#cfe3ec', fontSize: 12 };
function base(times: string[]): EChartsOption {
  return {
    animation: false,
    tooltip: { trigger: 'axis', appendToBody: true, confine: false, backgroundColor: '#052338f5', borderColor: '#319dca', textStyle: { color: '#e1f3ff', fontSize: 13 }, axisPointer: { type: 'line', lineStyle: { color: '#71d4ff', type: 'dashed' } } },
    legend: { top: 5, right: 12, itemWidth: 14, itemHeight: 7, textStyle: axisLabel },
    grid: { left: 48, right: 48, top: 50, bottom: 28 },
    xAxis: { type: 'category', boundaryGap: true, data: times, axisLabel: { ...axisLabel, hideOverlap: true, formatter: (value: string) => value.includes(' ') ? value.slice(11, 16) : value.slice(5) }, axisLine: { lineStyle: { color: '#6293ad66' } }, axisTick: { show: false } },
  };
}
function valueAxis(name: string, splitLine = true): Exclude<NonNullable<EChartsOption['yAxis']>, unknown[]> {
  return { type: 'value', name, nameTextStyle: { color: '#9fc8df', fontSize: 11 }, axisLabel, min: 0, splitLine: { show: splitLine, lineStyle: { color: '#b3d8f12b', type: 'dashed' } } };
}
export function monitoringOption(samples: MonitoringSample[], water = false): EChartsOption {
  const first = water ? '水位' : '降水量', second = water ? '流量' : '位移';
  const firstUnit = water ? 'm' : 'mm', secondUnit = water ? 'm³/s' : 'mm';
  return {
    ...base(samples.map(s => s.time)), color: water ? ['#f33187', '#ffb54d'] : ['#339cf3', '#00c7c2'],
    yAxis: [valueAxis(firstUnit), valueAxis(secondUnit, false)],
    series: [
      { name: first, type: water ? 'line' : 'bar', data: samples.map(s => water ? s.waterLevel : s.rainfall), barMaxWidth: 25, symbol: 'circle', symbolSize: 6, lineStyle: { width: 3 }, itemStyle: water ? {} : { borderRadius: [3, 3, 0, 0], color: { type: 'linear', x: 0, y: 0, x2: 0, y2: 1, colorStops: [{ offset: 0, color: '#9edfff' }, { offset: .35, color: '#339cf3' }, { offset: 1, color: '#13579266' }] } }, tooltip: { valueFormatter: value => `${value} ${firstUnit}` } },
      { name: second, type: 'line', yAxisIndex: 1, data: samples.map(s => water ? s.flow : s.displacement), symbol: 'circle', symbolSize: 6, itemStyle: { color: water ? '#ffb54d' : '#00c7c2' }, lineStyle: { width: 3, color: water ? '#ffb54d' : '#00c7c2' }, tooltip: { valueFormatter: value => `${value} ${secondUnit}` } },
    ],
  };
}
export function warningOption(samples: WarningSample[]): EChartsOption {
  return {
    ...base(samples.map(s => s.time)), color: ['#ff004c', '#ff9f0a', '#f1ff00', '#00baff'],
    legend: { top: 4, right: 5, itemWidth: 8, itemHeight: 8, icon: 'circle', textStyle: { ...axisLabel, fontSize: 11 } },
    grid: { left: 36, right: 12, top: 46, bottom: 26 }, yAxis: { ...valueAxis('条'), minInterval: 1 },
    series: (['red', 'orange', 'yellow', 'blue'] as const).map((key, i) => ({ name: ['红色', '橙色', '黄色', '蓝色'][i], type: 'line', data: samples.map(s => s[key]), symbol: 'circle', symbolSize: 6, lineStyle: { width: 2 }, tooltip: { valueFormatter: value => `${value} 条` } })),
  };
}
