<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import GalleryWrapper from '@/Components/Gallery/GalleryWrapper.vue';

defineProps({
    galleries: {
        type: Object
    }
});
</script>

<template>
    <GuestLayout :meta="{title: 'Galleries'}">
        <template #header> Galleries</template>
        <GalleryWrapper :galleries="galleries.data" user />
    </GuestLayout>
</template>
