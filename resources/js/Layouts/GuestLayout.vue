<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PWAinstall from '@/Components/PWAinstall.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import Footer from '@/Components/Footer/Footer.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';


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
    <section class="min-h-screen bg-black text-white pt-6 sm:pt-0">
        <MainNavbar v-if="page.url !== '/' && !$isPWA" />
        <header v-if="$slots.header" class="my-10 text-gray">
            <h1 class="text-xl font-bold text-center mb-5">
                <slot name="header" />
            </h1>
        </header>
        <main class="my-20 max-w-screen-sm md:max-w-screen-xl mx-auto">
            <slot />
        </main>
        <PWAinstall />
    </section>
    <Footer />
    <PwaNavbar v-if="page.url !== '/' && $isPWA" />
</template>
