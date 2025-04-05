<script setup>
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: false,
        default: 'rocker'
    }
});

const page = usePage();
</script>

<template>
    <MainNavbar v-if="page.url !== '/' && !$isPWA" />
    <div class="mt-20 mx-auto w-1/3 rounded-lg bg-graydark p-6">
        <slot />
    </div>
    <PwaNavbar v-if="page.url !== '/' && $isPWA" />
</template>
