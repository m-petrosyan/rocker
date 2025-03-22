<script setup>
import { Head } from '@inertiajs/vue3';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';

defineProps({
    meta: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Head :title="meta.title" />
    <div
        class="min-h-screen  bg-black text-white pt-6 sm:pt-0"
    >
        <MainNavbar />
        <div
        >
            <slot />
        </div>
    </div>
</template>
