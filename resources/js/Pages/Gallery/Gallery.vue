<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ImageWrapper from '@/Components/Wrappers/ImageWrapper.vue';
import NavLink from '@/Components/NavLink.vue';
import { computed } from 'vue';
import LocationIcon from '@/Components/Icons/LocationIcon.vue';
import CalendarIcon from '@/Components/Icons/CalendarIcon.vue';

const props = defineProps({
    gallery: {
        type: Object
    }
});

const venueName = computed(() => {
    return props.gallery.venue?.location ?? props.gallery.venue?.name;
});
</script>

<template>
    <GuestLayout :meta="{title: gallery.title}">
        <h1 class="text-center">{{ gallery.title }}</h1>
        <div class="flex flex-col-reverse md:flex-row mt-5 gap-y-6">
            <div class="md:w-1/3">
                <NavLink :href="route('profile.show', gallery.user.username)"
                         class="text-center flex flex-col items-center gap-y-4 p-2">
                    <div class="h-16 w-16 rounded-full overflow-hidden">
                        <img :src="gallery.user?.image?.thumb ?? '/images/user.jpg'" class="object-contain"
                             alt="">
                    </div>
                    <p>{{ gallery.user.name }}</p>
                </NavLink>
            </div>
            <div class="md:w-2/3 flex flex-col gap-y-4 bg-graydark p-4 rounded-lg text-pretty">
                <p>{{ gallery.description }}</p>
                <div v-if="venueName" class="flex gap-x-1 text-sm">
                    <LocationIcon />
                    <p>{{ venueName }}</p>
                </div>
                <p class="flex gap-x-1 bg-graydark2 w-fit px-1 rounded-sm">
                    <CalendarIcon />
                    <p>{{ gallery.date }}</p>
                </p>
                <div class="flex  flex-wrap gap-x-2">
                    <div v-for="band of gallery.bands" class="bg-red px-1 rounded-sm">
                        <div class="bg-red ">{{ band.name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <ImageWrapper :images="gallery.images_url" />
    </GuestLayout>
</template>
