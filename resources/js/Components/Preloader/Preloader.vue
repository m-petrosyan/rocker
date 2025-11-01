<script setup lang="ts">
import PreloaderIcon from '@/Components/Icons/PreloaderIcon.vue';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const visible = ref(false);


onMounted(() => {
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
