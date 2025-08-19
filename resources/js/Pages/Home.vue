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
    <GuestLayout :meta="{title: 'Home'}">
        <template #header><span class="hidden">Events, Bands & Galleries</span></template>
        <EventWrapper :events="events.data" more title="Events" />
        <BandWrapper :bands="bands.data" more title="Bands" />
        <GalleryWrapper :galleries="galleries.data" more title="Galleries" />
    </GuestLayout>
</template>
