<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    genres: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-wrap gap-x-2 gap-y-2">
        <Link
            v-for="genre in genres"
            :key="genre.id"
            :href="route('bands.index', { genre: genre.slug })"
            class="bg-red px-2 py-0.5 rounded-sm text-white hover:bg-orange transition-colors duration-300 text-sm font-medium"
        >
            {{ genre.name }}
        </Link>
    </div>
</template>
