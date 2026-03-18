<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  try {
    (window.adsbygoogle = window.adsbygoogle || []).push({});
  } catch (e) {
    console.error('AdSense error', e);
  }
});
</script>

<template>
  <div class="adsense-container h-full w-full flex items-center justify-center overflow-hidden">
    <ins class="adsbygoogle"
         style="display:block"
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
