<script setup>
import { onMounted, nextTick, ref, onBeforeUnmount } from 'vue';
import { isSSR } from '@/Helpers/ssrHelper.js';

const isBlocked = ref(false);
const isHidden = ref(false);
const insElement = ref(null);
let observer = null;

onMounted(() => {
  if (isSSR) return;

  const isLocal = window.location.hostname.includes('.loc') || window.location.hostname === 'localhost';
  if (isLocal) {
    isBlocked.value = true;
    return;
  }

  nextTick(() => {
    const timer = setTimeout(() => {
      // Критический таймаут для полной блокировки (скрипт не загружен)
      if (typeof window.adsbygoogle === 'undefined') {
        isBlocked.value = true;
      }
    }, 2000);

    // Следим за тем, что Google делает с блоком
    if (insElement.value) {
      observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.type === 'attributes' && mutation.attributeName === 'data-ad-status') {
            const status = insElement.value.getAttribute('data-ad-status');
            if (status === 'unfilled') {
              isHidden.value = true; // Скрываем блок совсем, если рекламы нет в инвентаре
            }
          }
        });
      });
      observer.observe(insElement.value, { attributes: true });
    }

    setTimeout(() => {
      try {
        if (typeof window.adsbygoogle !== 'undefined') {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
          clearTimeout(timer);
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

onBeforeUnmount(() => {
  if (observer) observer.disconnect();
});
</script>

<template>
  <div v-if="!isHidden" class="adsense-container w-full h-full overflow-hidden rounded-xl border border-white/5 bg-white/5 flex items-center justify-center">
    <div v-if="isBlocked" class="flex flex-col items-center gap-3 opacity-30 select-none">
       <span class="text-4xl">📢</span>
       <span class="text-xs uppercase tracking-widest font-bold text-center">AdSense<br />Placement</span>
    </div>
    <ins v-else
         ref="insElement"
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
