<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick } from "vue";
defineProps<{ src: string; alt?: string }>();
const holder = ref<HTMLSpanElement>();
const picture = ref<HTMLImageElement>();
const transform = ref("scale(1,1)");
let observer: ResizeObserver | undefined;
function resize() {
  const img = picture.value, parent = holder.value;
  if (!img?.complete || !img.naturalWidth || !parent) return;
  const style = window.getComputedStyle(img);
  const width = parseFloat(style.width) || img.naturalWidth;
  const height = parseFloat(style.height) || img.naturalHeight;
  const box = parent.getBoundingClientRect();
  // client dimensions are independent of the outer dashboard's screen scaling.
  const parentStyle = window.getComputedStyle(parent);
  const targetWidth = parseFloat(parentStyle.width) || box.width;
  const targetHeight = parseFloat(parentStyle.height) || box.height;
  transform.value = `scale(${targetWidth / width},${targetHeight / height})`;
}
onMounted(async () => { await nextTick(); observer = new ResizeObserver(resize); if (holder.value) observer.observe(holder.value); resize(); });
onBeforeUnmount(() => observer?.disconnect());
</script>
<template><span ref="holder" class="figma-image" aria-hidden="true"><img ref="picture" :src="src" :alt="alt || ''" :style="{ transform }" @load="resize" /></span></template>
<style scoped>
.figma-image { display: block; pointer-events: none; }
img { position: absolute; top: 0; left: 0; max-width: none; transform-origin: top left; }
</style>
