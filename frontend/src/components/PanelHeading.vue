<script setup lang="ts">
import DesignAsset from "./DesignAsset.vue";
withDefaults(defineProps<{ title: string; width?: number; more?: boolean }>(), { width: 361, more: false });
defineEmits<{ more: [] }>();
const base = "/figma/";
</script>
<template>
  <div class="panel-heading" :style="{ width: width + 'px' }">
    <DesignAsset class="heading-bg" :src="base + (width > 500 ? '7d1efd8b-9fce-4555-ae07-3c11bd53fcf7.svg' : 'c2e40012-d639-4457-95ed-eb3f2b95c539.svg')" :width="width" :height="38" />
    <DesignAsset class="heading-line" :src="base + (width > 500 ? '5b08f932-f2fd-443d-a478-384f86da2207.svg' : '6bc41246-62ef-4c66-ae1e-a15c1065955f.svg')" :width="width - 1" :height="1" />
    <DesignAsset class="heading-end" :src="base + 'e218883f-3290-49d1-83d2-4e27279ad5ec.svg'" :width="44" :height="18.034" />
    <DesignAsset class="heading-edge" :src="base + '32486c98-c076-449f-96f7-b90ac4d7bf7f.svg'" :width="35.94" :height="4.03" />
    <DesignAsset class="heading-slash-small" :src="base + '5e1cf958-3feb-4eb4-a23f-8514874e1967.svg'" :width="10.992" :height="22.113" />
    <DesignAsset class="heading-slash" :src="base + '70864177-c3ed-4558-aa9a-c5e614fcb135.svg'" :width="16.691" :height="38" />
    <DesignAsset class="heading-glow" :src="base + 'f7b28a14-907a-4c58-8c0a-e4002b6fd0ac.svg'" :width="38.164" :height="50.077" />
    <h2>{{ title }}</h2>
    <button v-if="more" class="more-button" @click="$emit('more')">更多&gt;</button>
  </div>
</template>
