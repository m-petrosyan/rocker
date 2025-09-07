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
        :meta="{title: 'Bands - Discover Armenian rock and metal bands', keywords: 'armenian rock, armenian metal, armenian rock bands, armenian metal bands, armenia music, armenian hard rock, armenian heavy metal, armenian punk, armenian alternative rock, armenian folk metal, yerevan rock scene, armenian rock concerts, armenian underground music, armenian progressive rock, armenian thrash metal, armenian doom metal, armenian gothic rock, armenian post-rock, armenian music portal'+ bands.data.map(band => band.name).join(', ')}">
        <template #header> Bands</template>
        <template #h1> Discover Armenian rock and metal bands</template>
        <BandWrapper :bands="bands.data" user add />
    </GuestLayout>
</template>
