<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';
import Footer from '@/Components/Footer/Footer.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import { computed, onMounted, ref } from 'vue';
import { isWebApp } from '@/Helpers/setAppUser.js';
import defaultImg from '/public/screenshots/desktop-screenshot.png';
import Preloader from '@/Components/Preloader/Preloader.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';

const props = defineProps({
  meta: Object
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const defaultTitle = 'Rocker.am';
const defaultDescription =
  'Discover Armenian rock and metal bands, concerts, news, and the underground music scene in Armenia.';
const defaultKeywords =
  'Armenian, Rock, Metal, Yerevan, Music, Punk, Alternative, Heavy Metal, Bands';

const cleanDescription = computed(() => {
  const raw = props.meta?.description ?? defaultDescription;
  const text = raw.replace(/<[^>]*>/g, '');
  const shortened = text.slice(0, 150);
  return shortened.slice(0, shortened.lastIndexOf(' ')) || shortened;
});

const isPWA = ref(false);
const webApp = isWebApp();


onMounted(() => {
  isPWA.value =
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true;
});

// useTelegramAuth();
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
    <link rel="canonical" :href="$page.props.canonical" />
  </Head>
  <MainNavbar v-if="!(isPWA || webApp)" />
  <FleshNotification />
  <slot />
  <Footer v-if="!webApp" />
  <Preloader />
  <PwaNavbar v-if="isPWA || webApp" />
</template>
