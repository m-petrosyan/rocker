<script setup>
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import { Head, usePage } from '@inertiajs/vue3';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import { onMounted, ref } from 'vue';
import Footer from '@/Components/Footer/Footer.vue';

defineProps({
    title: {
        type: String,
        required: false,
        default: 'rocker'
    }
});

const page = usePage();

const isPWA = ref(false);

onMounted(() => {
    isPWA.value = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true;
});
</script>

<template>
    <Head :title />
    <section class=" text-white pt-6 sm:pt-0">
        <MainNavbar v-if="page.url !== '/' && !isPWA" />
        <main class="mt-20 sm:mx-auto w-full sm:w-8/12 md:w-4/12 md:max-w-[500px] rounded-lg  bg-graydark2 p-6">
            <ErrorMessages :messages="$page.props.errors" />
            <slot />
        </main>
        <slot name="underslot" />
    </section>
    <Footer />
    <PwaNavbar v-if="isPWA" />
</template>
