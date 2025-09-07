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
        :meta="{title: 'Galleries - Armenian Rock & Metal Concert Galleries', keywords: 'armenian rock events, armenian metal events, armenian rock concerts, armenian metal concerts, armenian rock festivals, armenian metal festivals, yerevan rock events, yerevan metal concerts, armenian live rock, armenian live metal, armenian concert gallery, armenian rock gallery, armenian metal gallery, armenian concert photos, armenian rock photography, armenian metal photography, armenian music events, armenian underground concerts, armenian underground music events'+ galleries.data.map(gallery => gallery.title).join(', ')}">
        <template #header> Galleries</template>
        <template #h1> Photo Galleries of Armenian Rock & Metal Concerts</template>
        <GalleryWrapper :galleries="galleries.data" :isAdmin="auth.isAdmin" user add />
    </GuestLayout>
</template>
