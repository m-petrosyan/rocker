<script setup lang="ts">
import PreloaderIcon from '@/Components/Icons/PreloaderIcon.vue';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { emitter } from '@/Helpers/event-bus.js';

const visible = ref(false);


onMounted(() => {
  emitter.on('preloader-toggle', toggle => {
    visible.value = toggle;
  });

  router.on('start', () => {
    visible.value = true;
  });

  router.on('finish', () => {
    // window.scrollTo({ top: 0 });
    visible.value = false;
  });
});
</script>

<template>
  <PreloaderIcon v-if="visible" />
</template>
