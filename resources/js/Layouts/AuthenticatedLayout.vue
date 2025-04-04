<script setup>
import { Head } from '@inertiajs/vue3';
import AuthNavbar from '@/Components/Nav/AuthNavbar.vue';
import FleshNotification from '@/Components/Messages/FleshNotification.vue';

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
        <AuthNavbar />
        <header v-if="$slots.header" class="my-10 text-gray">
            <h1 class="text-xl font-bold text-center mb-5">
                <slot name="header" />
            </h1>
        </header>
        <!-- Page Content -->
        <main class="my-20 max-w-screen-sm md:max-w-screen-xl mx-auto">
            <slot />
        </main>
    </section>
</template>
