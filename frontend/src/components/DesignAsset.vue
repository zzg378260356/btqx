<script setup lang="ts">
import { computed, ref } from "vue";
const props = defineProps<{ src: string; width: number; height: number }>();
const native = ref({ width: props.width, height: props.height });
const transform = computed(() => `scale(${props.width / native.value.width}, ${props.height / native.value.height})`);
function loaded(event: Event) {
  const img = event.target as HTMLImageElement;
  // SVG intrinsic sizes can be fractional; naturalWidth rounds them to integers.
  const computedStyle = window.getComputedStyle(img);
  native.value = { width: parseFloat(computedStyle.width) || img.naturalWidth || props.width, height: parseFloat(computedStyle.height) || img.naturalHeight || props.height };
}
</script>
<template>
  <span class="design-asset" :style="{ width: width + 'px', height: height + 'px' }" aria-hidden="true">
    <img :src="src" alt="" @load="loaded" :style="{ transform }" />
  </span>
</template>
<style scoped>
.design-asset { display: inline-block; position: relative; pointer-events: none; }
img { position: absolute; left: 0; top: 0; max-width: none; transform-origin: top left; }
</style>
