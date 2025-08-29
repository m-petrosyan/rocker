<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';
import { Ziggy } from '@/ziggy.js';

defineProps({
    auth: {
        type: Object,
        required: true
    },
    events: {
        type: Object,
        required: true
    },
    galleries: {
        type: Object,
        required: true
    },
    bands: {
        type: Object,
        required: true
    }
});

console.log(Ziggy);
</script>

<template>
    <GuestLayout :meta="{title: 'Armenian Rock & Metal Bands, Concerts & Galleries'}">
        <template #h1>Armenian Rock & Metal Bands, Concerts & Galleries</template>
        <p class="text-pretty text-gray mb-6">Welcome to Rocker.am – your portal for Armenian rock and metal bands.
            Discover
            upcoming
            concerts, explore
            photo galleries from live performances, and stay updated with the Armenian music scene.</p>
        <EventWrapper :events="events.data" more title="Events" />
        <BandWrapper :bands="bands.data" more title="Bands" />
        <GalleryWrapper :galleries="galleries.data" more title="Galleries" />
    </GuestLayout>
</template>
