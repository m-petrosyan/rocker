<script setup>
import { Head } from '@inertiajs/vue3';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';

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
    <FleshNotification />
    <section class="min-h-screen bg-black text-white pt-6 sm:pt-0">
        <MainNavbar v-if="!$isPWA" />
        <header v-if="$slots.header" class="my-10 text-gray">
            <h1 class="text-center mb-5">
                <slot name="header" />
            </h1>
        </header>
        <main
            class="relative my-28 bg-graydark py-2 sm:px-6 lg:p-4 md:px-0  max-w-screen-sm md:max-w-screen-xl mx-auto">
            <ErrorMessages :messages="$page.props.errors" />
            <slot />
        </main>
    </section>
    <PwaNavbar v-if="$isPWA" />
</template>
