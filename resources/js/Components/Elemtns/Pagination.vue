<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Object
});

// функция для получения только ?page=...
const getPageQuery = (url) => {
    if (!url) return null;
    const u = new URL(url);
    return `?${u.searchParams.toString()}`;
};
</script>

<template>
    <div class="flex justify-center gap-4 mt-6">
        <Link
            v-if="links.prev"
            :href="getPageQuery(links.prev)"
            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300"
        >
            « Prev
        </Link>

        <Link
            v-if="links.next"
            :href="getPageQuery(links.next)"
            class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300"
        >
            Next »
        </Link>
    </div>
</template>
