<script setup lang="ts">
import DesignAsset from "./DesignAsset.vue";
withDefaults(defineProps<{ names: Record<string, string>; title?: string; suffix?: string }>(), { title: "预警释义", suffix: "预警" });
defineEmits<{ select: [level: string] }>();
const base = "/figma/";
const decorations = [
  { src: "e2e8fa7d-db50-40d4-9d25-46c2c8410621.svg", width: 83.9864, height: 68.272, right: 132.65, top: -26.68, mask: "70.634px 26.681px" },
  { src: "65751668-98a4-4bcb-ad1d-486b07c254d1.svg", width: 67.122, height: 68.272, right: 32.13, top: -26.68, mask: "-46.749px 26.681px" },
  { src: "9dcfe7da-923a-4a6b-aad6-e5f164f7966e.svg", width: 74.7877, height: 68.272, right: 78.56, top: -26.68, mask: "7.345px 26.681px" },
  { src: "736fe9ca-ce27-4c64-b04e-5a7382e1d4cc.svg", width: 60.9895, height: 68.272, right: -8.17, top: -26.68, mask: "-93.176px 26.681px" },
  { src: "aaec0470-edbc-47ba-8e1f-da2dcb0a8257.svg", width: 133.896, height: 42, left: 0, top: 0, mask: "0 0" },
  { src: "3a4383fd-fd49-42cc-9651-b7b7b31469d9.svg", width: 58.5318, height: 42, left: 0, top: 0, mask: "0 0" },
];
const dots: Record<string, string> = {
  red: "bb9ebc5b-c9a6-4650-aaa9-54d0a846af68.svg",
  orange: "c3057ef1-1e9b-43bd-baae-8e3f9ecb5083.svg",
  yellow: "7a45cc0d-f2ea-4a41-9359-878963e55fc7.svg",
  blue: "dfc4ae20-513e-44c4-bfe0-4236d317ba33.svg",
};
</script>
<template>
  <div class="map-legend">
    <div class="legend-title">
      <DesignAsset style="position:absolute;left:0;top:0" :src="base + '318548fc-e156-450a-9e98-e63b00708e6d.svg'" :width="218" :height="42" />
      <DesignAsset v-for="d in decorations" :key="d.src" class="legend-decoration" :src="base + d.src" :width="d.width" :height="d.height" :style="{ right: d.right === undefined ? undefined : d.right + 'px', left: d.left === undefined ? undefined : d.left + 'px', top: d.top + 'px', maskImage: 'url(' + base + 'e07405c1-b261-4c1d-b29b-61867f317aba.svg)', maskPosition: d.mask }" />
      <DesignAsset class="legend-icon" :src="base + 'a151f372-66e5-4c84-91d8-88bc4b890d21.svg'" :width="31.0273" :height="31.9588" />
      <h2>{{ title }}</h2>
    </div>
    <div class="legend-items"><button v-for="(name, key) in names" :key="key" @click="$emit('select', String(key))"><DesignAsset :src="base + dots[key]" :width="10" :height="10" /><span>{{ name }}{{ suffix }}</span></button></div>
  </div>
</template>
<style scoped>
.legend-title { position: relative; height: 42px; overflow: hidden; }
.legend-decoration { position: absolute; mask-size: 218px 42px; mask-repeat: no-repeat; }
.legend-title h2 { left: 38px; top: 7.6px; font-weight: 500; }
.legend-icon { position: absolute; left: 1.54px; top: 8.6px; }
.legend-items { height: 134px; padding: 8px; background: radial-gradient(ellipse at bottom left, #3b8dff40, #1d56c21f 50%, transparent), #10121bcc; }
.legend-items button { display: flex; align-items: center; justify-content: center; gap: 4px; height: 29.5px; margin: 0; font-size: 20px; padding-bottom: 4px; }
.legend-items button:nth-child(-n+2) { border-bottom: 1px solid #b5caff; }
</style>
