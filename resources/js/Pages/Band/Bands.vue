<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';

defineProps({
  bands: {
    type: Object
  }
});
</script>

<template>
  <GuestLayout
    :meta="{
        title: 'Bands - Discover Armenian rock and metal bands',
        description: 'Discover Armenian rock and metal bands. Explore profiles, genres, photos, and stories from the Yerevan rock and metal scene on Rocker.am.',
        keywords: 'armenian rock bands, armenian metal bands, yerevan rock scene, armenian hard rock, armenian heavy metal, armenian punk, armenian alternative rock, armenian folk metal, armenian thrash metal, armenian progressive rock, armenian doom metal, armenian post-rock, armenian gothic rock, armenian underground music' + ', ' + bands.data.map(band => band.name).join(', ')
    }">
    <template #header> Bands</template>
    <div class="text-gray text-pretty">
      <h2 class="text-lg font-medium text-gray mb-6">Armenian Rock & Metal Bands</h2>
      <p>Discover the best Armenian rock and metal bands on <strong>Rocker.am</strong>. From legendary pioneers of
        the Yerevan scene to emerging artists, explore the musicians shaping Armenia’s rock and metal sound.</p>
    </div>
    <template #h1> Discover Armenian rock and metal bands</template>
    <BandWrapper :bands="bands.data" user add />
    <div class="text-gray text-pretty mt-10">
      <p>Each band page on <strong>Rocker.am</strong> photos, videos, and information about
        upcoming concerts. Stay connected with the Armenian rock scene and follow your favorite groups.</p>
    </div>
  </GuestLayout>
</template>
