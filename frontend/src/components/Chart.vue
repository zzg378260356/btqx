<script setup lang="ts">
import { onMounted, onBeforeUnmount, watch, ref } from "vue";
import * as echarts from "echarts";
const props = defineProps<{ option: echarts.EChartsOption }>();
const el = ref<HTMLDivElement>();
let chart: echarts.ECharts | undefined;
let observer: ResizeObserver;
onMounted(() => {
  chart = echarts.init(el.value!);
  chart.setOption(props.option);
  observer = new ResizeObserver(() => chart?.resize());
  observer.observe(el.value!);
});
watch(
  () => props.option,
  (v) => chart?.setOption(v),
);
onBeforeUnmount(() => {
  observer?.disconnect();
  chart?.dispose();
});
</script>
<template><div ref="el" class="chart"></div></template>
