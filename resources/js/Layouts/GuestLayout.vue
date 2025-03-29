<script setup>
import { Head } from '@inertiajs/vue3';
import PWAinstall from '@/Components/PWAinstall.vue';

defineProps({
    meta: {
        type: Object,
        required: false,
        default: 'rocker'
    }
});
</script>

<template>
    <Head :title="meta.title" />
    <div
        class="min-h-screen  bg-black text-white pt-6 sm:pt-0"
    >
        <!--        <MainNavbar />-->
        <div
        >
            <slot />
            <PWAinstall />
        </div>
    </div>
</template>
