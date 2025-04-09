<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PWAinstall from '@/Components/PWAinstall.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import Footer from '@/Components/Footer/Footer.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';
import defaultImg from '/public/screenshots/desktop-screenshot.png';

defineProps({
    meta: {
        type: Object,
        required: false
    }
});

const defaultDescription = 'The Heart of Armenian Rock';
const defaultTitle = 'Rocker.am';
const page = usePage();
</script>

<template>
    <Head :title="meta?.title ?? defaultTitle">
        <meta name="description" :content="meta?.description ??  defaultDescription" />
        <meta name="og:description" :content="meta?.description ?? defaultDescription" />
        <meta name="og:title" :content="meta?.title ?? defaultTitle" />
        <meta name="keywords" :content="meta?.keywords ?? 'Armenian, Rock, Music'" />
        <meta name="og:image" :content="meta?.image ?? defaultImg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:image" :content="meta?.image ?? defaultImg" />
    </Head>
    <FleshNotification />
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
