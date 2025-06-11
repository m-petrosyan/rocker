<script setup>
import { Head } from '@inertiajs/vue3';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import defaultImg from '../../../public/screenshots/desktop-screenshot.png';
import { onMounted, ref } from 'vue';
import Footer from '@/Components/Footer/Footer.vue';
import PreloaderPwa from '@/Components/Preloader/PreloaderPwa.vue';

defineProps({
    meta: {
        type: Object,
        required: false,
        default: 'rocker'
    }
});

const defaultDescription = 'The Heart of Armenian Rock';
const defaultTitle = 'Rocker.am';

const isPWA = ref(false);

onMounted(() => {
    isPWA.value = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;
});
</script>

<template>
    <Head :title="meta?.title ?? defaultTitle">
        <meta name="description" :content="meta?.description ??  defaultDescription" />
        <meta name="og:description" :content="meta?.description ?? defaultDescription" />
        <meta name="og:title" :content="meta?.title+' rocker.am' ?? defaultTitle" />
        <meta name="keywords" :content="meta?.keywords ?? 'Armenian, Rock, Music'" />
        <meta name="og:image" :content="meta?.image ?? defaultImg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:image" :content="meta?.image ?? defaultImg" />
        <meta name="author" :content="meta?.author ?? 'rocker.am'" />
    </Head>
    <FleshNotification />
    <PreloaderPwa v-if="isPWA" />
    <section class="min-h-screen bg-black text-white pt-6 sm:pt-0">
        <MainNavbar v-if="!isPWA" />
        <header v-if="$slots.header" class="my-10 text-gray">
            <h1 class="text-center mb-5">
                <slot name="header" />
            </h1>
        </header>
        <main
            class="profile relative my-28 bg-graydark py-2 sm:px-6 lg:p-4 md:px-0  max-w-screen-sm md:max-w-screen-xl mx-auto">
            <ErrorMessages :messages="$page.props.errors" />
            <slot />
        </main>
    </section>
    <Footer />
    <PwaNavbar v-if="isPWA" />
</template>
