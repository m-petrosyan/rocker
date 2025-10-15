<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array
});

// извлекаем page query из полного URL
const getPageQuery = (url) => {
  if (!url) return null;
  const u = new URL(url);
  return `?${u.searchParams.toString()}`;
};
</script>

<template>
  <div class="flex justify-center gap-2 mt-6">
    <template v-for="(link, i) in links" :key="i">
      <Link
        v-if="link.url"
        :href="getPageQuery(link.url)"
        class="px-3 py-1 rounded"
        :class="[
          link.active
            ? 'bg-orange text-white'
            : 'hover:bg-orange/50'
        ]"
        v-html="link.label"
      />
      <span
        v-else
        class="px-3 py-1 text-gray-400"
        v-html="link.label"
      />
    </template>
  </div>
</template>
