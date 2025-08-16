<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import PWAinstall from '@/Components/PWAinstall.vue';
import Footer from '@/Components/Footer/Footer.vue';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';
import defaultImg from '/public/screenshots/desktop-screenshot.png';
import { computed, onMounted, ref } from 'vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import PreloaderPwa from '@/Components/Preloader/PreloaderPwa.vue';

const props = defineProps({
    meta: {
        type: Object,
        required: false
    }
});

const defaultTitle = 'Rocker.am';
const defaultDescription = 'Discover Armenian rock and metal bands, concerts, news, and the underground music scene in Armenia.';
const defaultKeywords = 'Armenian, Rock,Rock Armenia, Music, Metal, Yerevan, Armenia, Heavy Metal, Punk, Alternative, Gothic, Progressive Rock, Doom Metal, Armenian Bands, Armenian Rock Bands, Armenian Metal Bands, Rock Concerts Armenia, Metal Concerts Armenia, Armenian Rock Scene, Underground Rock Armenia, Rock Festivals Armenia';
const page = usePage();

const cleanDescription = computed(() => {
    const raw = props.meta?.description ?? defaultDescription;
    const text = raw.replace(/<[^>]*>/g, '');
    return text.slice(0, 160);
});

const isPWA = ref(false);

onMounted(() => {
    isPWA.value = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;
});
</script>

<template>
    <Head :title="meta?.title ?? defaultTitle">
        <meta name="description" :content="cleanDescription" />
        <meta name="og:description" :content="cleanDescription" />
        <meta name="og:title" :content="meta?.title ?? defaultTitle" />
        <meta name="keywords" :content="meta?.keywords ?? defaultKeywords" />
        <meta name="og:image" :content="meta?.image ?? defaultImg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:image" :content="meta?.image ?? defaultImg" />
        <meta name="author" :content="meta?.author ?? 'rocker.am'" />
    </Head>
    <PreloaderPwa v-if="isPWA" />
    <FleshNotification />
    <section class="min-h-screen text-white pt-6 sm:pt-0 mb-40">
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
