<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ImageWrapper from '@/Components/Wrappers/ImageWrapper.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
    gallery: {
        type: Object
    }
});
</script>

<template>
    <GuestLayout :meta="{title: gallery.title}">
        <h1 class="text-center">{{ gallery.title }}</h1>
        <p class="text-center text-gray-500">{{ gallery.description }}</p>
        <p class="text-center">{{ gallery.date }}</p>
        <NavLink :href="route('profile.show', gallery.user.username)" class="text-center">
            by {{ gallery.user.name }}
        </NavLink>
        <ImageWrapper :images="gallery.images_url" />
    </GuestLayout>
</template>
