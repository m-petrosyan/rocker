<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';

defineProps({
    events: {
        type: Object,
        required: true
    },
    galleries: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <GuestLayout :meta="{title: 'Home'}">
        <h2 class="text-center mb-5">
            Events
        </h2>
        <EventWrapper :events more />
        <h2 class="text-center my-5">
            Galleries
        </h2>
        <GalleryWrapper :galleries more />
    </GuestLayout>
</template>
