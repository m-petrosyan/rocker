<script setup>
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import { Head, usePage } from '@inertiajs/vue3';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';

defineProps({
    title: {
        type: String,
        required: false,
        default: 'rocker'
    }
});

const page = usePage();
</script>

<template>
    <Head :title />
    <FleshNotification />
    <section class="min-h-screen bg-black text-white pt-6 sm:pt-0">
        <MainNavbar v-if="page.url !== '/' && !$isPWA" />
        <header v-if="$slots.header" class="my-10 text-gray">
            <h1 class="text-xl font-bold text-center mb-5">
                <slot name="header" />
            </h1>
        </header>
        <main class="relative my-28 bg-graydark py-2 sm:px-6 lg:px-8  max-w-screen-sm md:max-w-screen-xl mx-auto">
            <ErrorMessages :messages="$page.props.errors" />
            <slot />
        </main>
        <PwaNavbar v-if="page.url !== '/' && $isPWA" />
    </section>
</template>
