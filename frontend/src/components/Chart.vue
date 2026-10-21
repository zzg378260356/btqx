<script setup lang="ts">
import { onMounted, onBeforeUnmount, watch, ref } from "vue";
import * as echarts from "echarts";
const props = defineProps<{ option: echarts.EChartsOption }>();
const el = ref<HTMLDivElement>();
let chart: echarts.ECharts | undefined;
let observer: ResizeObserver;
function positionedOption(option: echarts.EChartsOption): echarts.EChartsOption {
  if (!option.tooltip) return option;
  const position: echarts.TooltipComponentPositionCallback = (point, _params, dom, _rect, size) => {
    const bounds = el.value!.getBoundingClientRect();
    const sx = bounds.width / el.value!.clientWidth || 1;
    const sy = bounds.height / el.value!.clientHeight || 1;
    const tip = dom instanceof HTMLElement ? dom.getBoundingClientRect() : undefined;
    const width = tip?.width || size.contentSize[0]!;
    const height = tip?.height || size.contentSize[1]!;
    const cursorX = bounds.left + point[0]! * sx;
    const cursorY = bounds.top + point[1]! * sy;
    // Position in screen pixels, then convert back to chart coordinates. This also
    // handles dashboards scaled by CSS without sending tooltips off the viewport.
    const x = Math.max(8, Math.min(cursorX + 12, window.innerWidth - width - 8));
    const y = Math.max(8, Math.min(cursorY - height - 12, window.innerHeight - height - 8));
    return [(x - bounds.left) / sx, (y - bounds.top) / sy];
  };
  const configure = (tooltip: echarts.TooltipComponentOption) => ({ ...tooltip, className: 'dashboard-chart-tooltip', appendToBody: true, confine: false, transitionDuration: 0, hideDelay: 0, position });
  return { ...option, tooltip: Array.isArray(option.tooltip) ? option.tooltip.map(configure) : configure(option.tooltip) };
}
onMounted(() => {
  chart = echarts.init(el.value!);
  chart.setOption(positionedOption(props.option));
  observer = new ResizeObserver(() => chart?.resize());
  observer.observe(el.value!);
});
watch(
  () => props.option,
  (v) => chart?.setOption(positionedOption(v), { notMerge: true }),
);
onBeforeUnmount(() => {
  observer?.disconnect();
  chart?.dispose();
});
</script>
<template><div ref="el" class="chart"></div></template>
