<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    bands: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-wrap gap-x-2 gap-y-2">
        <component
            v-for="band in bands"
            :key="band.name"
            :is="band.info ? Link : 'div'"
            :href="route('bands.show',band.slug)"
            class="bg-red px-1 rounded-sm">
            <div class="bg-red ">{{ band.name }}</div>
        </component>
    </div>
</template>
