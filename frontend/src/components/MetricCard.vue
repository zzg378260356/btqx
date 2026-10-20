<script setup lang="ts">
import DesignAsset from "./DesignAsset.vue";
defineProps<{ caption: string; value: number; unit: string; label: string }>();
const b = "/figma/";
const layers = [
  { src: "ae1af2f5-668e-4265-932e-39b8d5470073.svg", x: -2, y: 8, w: 76.1, h: 76 },
  { src: "2ffceb01-5663-41c7-b4fb-283891460863.svg", x: 4.515, y: 14.514, w: 63.05, h: 63.05 },
  { src: "9a462e66-5ec5-45a3-bc36-98b879749307.svg", x: 4.515, y: 14.514, w: 63.05, h: 63.05 },
  { src: "9bf4f15c-25e9-49d8-8054-03abfa508fca.svg", x: 1.1, y: 11.5, w: 60.5, h: 60.5 },
  { src: "e8d26869-fd5d-449a-868e-b37211b82e6b.svg", x: 4.515, y: 14.514, w: 54.44, h: 54.44 },
  { src: "8bfd8b66-f549-4b0a-b1cc-c2c7acfdbafa.svg", x: 4.515, y: 14.514, w: 54.44, h: 54.44 },
  { src: "e92cb5f6-2b7e-4f1b-bee4-526671f5a2dc.svg", x: 17.75, y: 27.72, w: 36.94, h: 36.94 },
  { src: "6953f335-9e37-444d-a0a0-1d217af5f3cd.svg", x: 42.5, y: 50.6, w: 23.7, h: 27.3 },
  { src: "d3b07732-657d-4f9b-8e56-88a979434c7c.svg", x: 33.3, y: 42.7, w: 35.1, h: 35.1 },
  { src: "3aed29e3-43bb-4a6f-93c0-e696b9f0bbd9.svg", x: 3.3, y: 12.2, w: 37.5, h: 37, rotate: 180 },
  { src: "5d9c36e2-2108-470d-8471-2b37d9c6bb3f.svg", x: 10, y: 19.2, w: 23.3, h: 23.3, rotate: 180 },
  { src: "d33b9fdb-ac89-470e-ac22-a5bb4a7eec51.png", x: 11, y: 14, w: 51, h: 52 },
];
</script>
<template>
  <article class="metric-card">
    <DesignAsset class="metric-back" :src="b + '2e43239c-f761-4d52-a1a4-d23e6fb4f8d6.svg'" :width="76" :height="211" />
    <DesignAsset v-for="layer in layers" :key="layer.src" class="metric-layer" :src="b + layer.src" :width="layer.w" :height="layer.h" :style="{ left: layer.x + 'px', top: layer.y + 'px', transform: layer.rotate ? 'rotate(' + layer.rotate + 'deg)' : undefined }" />
    <div class="metric-copy"><span>{{ caption }}</span><strong>{{ value }}<small>{{ unit }}</small></strong><em>{{ label }}</em></div>
  </article>
</template>
