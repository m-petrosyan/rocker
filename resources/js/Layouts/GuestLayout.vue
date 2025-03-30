<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PWAinstall from '@/Components/PWAinstall.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';


defineProps({
    meta: {
        type: Object,
        required: false,
        default: 'rocker'
    }
});

const page = usePage();
</script>

<template>
    <Head :title="meta.title" />
    <div
        class="min-h-screen  bg-black text-white pt-6 sm:pt-0"
    >
        <MainNavbar v-if="page.url !== '/'" />
        <div
        >
            <main class="my-20">
                <slot />
            </main>
            <PWAinstall />
        </div>
    </div>
</template>
