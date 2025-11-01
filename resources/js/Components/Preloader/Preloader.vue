<script setup lang="ts">
import PreloaderIcon from '@/Components/Icons/PreloaderIcon.vue';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const visible = ref(false);
const prevPath = ref(window.location.pathname);

onMounted(() => {
  router.on('start', () => {
    prevPath.value = window.location.pathname;
    visible.value = true;
  });

  router.on('finish', () => {
    if (window.location.pathname !== prevPath.value) {
      window.scrollTo({ top: 0 });
    }
    visible.value = false;
  });
});
</script>

<template>
  <PreloaderIcon v-if="visible" />
</template>
