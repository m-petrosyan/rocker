<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PWAinstall from '@/Components/PWAinstall.vue';
import Footer from '@/Components/Footer/Footer.vue';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';
import defaultImg from '/public/screenshots/desktop-screenshot.png';
import { onMounted, ref } from 'vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';

defineProps({
    meta: {
        type: Object,
        required: false
    }
});

const defaultDescription = 'The Heart of Armenian Rock';
const defaultTitle = 'Rocker.am';
const page = usePage();

const isPWA = ref(false);

onMounted(() => {
    // Detect PWA mode
    isPWA.value = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;
});
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
        <meta name="author" :content="meta?.author ?? 'rocker.am'" />
    </Head>
    <FleshNotification />
    <section class="min-h-screen text-white pt-6 sm:pt-0">
        <MainNavbar v-if="!isPWA" />
        <header v-if="$slots.header" class="my-10 text-gray">
            <h1 class="text-center mb-5">
                <slot name="header" />
            </h1>
        </header>
        <main class="my-20 max-w-screen-sm md:max-w-screen-xl mx-auto">
            <slot />
        </main>
        <PWAinstall />
    </section>
    <Footer />
    <PwaNavbar v-if="isPWA" />
</template>
