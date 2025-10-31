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
    <template #header> Galleries</template>
    <div class="text-gray text-pretty">
      <h2 class="text-lg font-medium text-gray mb-6">Armenian Rock & Metal Concert Galleries/h2>
      <p>Our galleries feature images from past concerts, festivals, and underground events, showcasing the vibrant
        atmosphere of the Armenian rock and metal scene.</p>
    </div>
    <template #h1> Photo Galleries of Armenian Rock & Metal Concerts</template>
    <GalleryWrapper :galleries="galleries.data" :isAdmin="auth.isAdmin" user add />
    <div class="text-gray text-pretty mt-10">
      <p>All galleries are published in full resolution, without cropping or quality loss. If selected, photos may also
        appear on our official Instagram page, always with proper credit to the author.</p>
    </div>
  </GuestLayout>
</template>
