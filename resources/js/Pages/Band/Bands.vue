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
        <template #h1> Discover Armenian rock and metal bands</template>
        <BandWrapper :bands="bands.data" user add />
    </GuestLayout>
</template>
