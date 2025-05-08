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
    <GuestLayout :meta="{title: 'Bands'}">
        <template #header> Bands</template>
        <BandWrapper :bands="bands.data" user add />
    </GuestLayout>
</template>
