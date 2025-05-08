<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';

defineProps({
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

</script>

<template>
    <GuestLayout :meta="{title: 'Home'}">
        <EventWrapper :events more title="Events" />
        <BandWrapper :bands="bands.data" more title="bands" />
        <GalleryWrapper :galleries="galleries.data" more title="Galleries" />
    </GuestLayout>
</template>
