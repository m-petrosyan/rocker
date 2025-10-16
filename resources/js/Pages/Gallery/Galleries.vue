<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';

defineProps({
  galleries: {
    type: Object
  },
  auth: {
    type: Object,
    default: () => ({})
  }
});
</script>

<template>
  <GuestLayout
    :meta="{
            title: 'Galleries – Armenian Rock & Metal Concert Photos | Rocker.am',
            description: 'Browse Armenian rock and metal concert galleries with photos from live shows, festivals, and underground events in Yerevan and across Armenia.',
            keywords: 'Armenian rock and metal concert galleries, including photos from live shows, festivals, underground events, and music photography in Yerevan and across Armenia. ' + galleries.data.map(gallery => gallery.title).join(', ')
        }">
    <template #header> Events</template>
    <template #h1> Photo Galleries of Armenian Rock & Metal Concerts</template>
    <GalleryWrapper :galleries="galleries.data" :isAdmin="auth.isAdmin" user add />
  </GuestLayout>
</template>
