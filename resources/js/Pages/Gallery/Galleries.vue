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
    <GuestLayout :meta="{title: 'Galleries'}">
        <template #header> Galleries</template>
        <GalleryWrapper :galleries="galleries.data" :isAdmin="auth.isAdmin" user add />
    </GuestLayout>
</template>
