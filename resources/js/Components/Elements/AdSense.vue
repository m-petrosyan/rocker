<script setup>
import { onMounted, nextTick, ref } from 'vue';
import { isSSR } from '@/Helpers/ssrHelper.js';

const isBlocked = ref(false);

onMounted(() => {
  if (isSSR) return;

  // Проверка на локальную среду
  const isLocal = window.location.hostname.includes('.loc') || window.location.hostname === 'localhost';
  
  if (isLocal) {
    isBlocked.value = true;
    return;
  }

  nextTick(() => {
    // Если через 2 секунды реклама не подала признаков жизни, считаем её заблокированной
    const timer = setTimeout(() => {
      isBlocked.value = true;
    }, 2000);

    setTimeout(() => {
      try {
        if (typeof window.adsbygoogle !== 'undefined') {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
          clearTimeout(timer); // Реклама пошла, отменяем заглушку
        } else {
          isBlocked.value = true;
        }
      } catch (e) {
        console.error('AdSense error', e);
        isBlocked.value = true;
      }
    }, 100);
  });
});
</script>

<template>
  <div class="adsense-container w-full h-full overflow-hidden rounded-xl border border-white/5 bg-white/5 flex items-center justify-center">
    <div v-if="isBlocked" class="flex flex-col items-center gap-3 opacity-30 select-none">
       <span class="text-4xl">📢</span>
       <span class="text-xs uppercase tracking-widest font-bold">AdSense Placement</span>
    </div>
    <ins v-else
         class="adsbygoogle"
         style="display:block;width:100%;height:100%;min-width:250px;min-height:250px"
         data-ad-client="ca-pub-3905150332935722"
         data-ad-slot="9967456797"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
  </div>
</template>

<style scoped>
.adsense-container {
  min-height: 250px;
}
</style>
