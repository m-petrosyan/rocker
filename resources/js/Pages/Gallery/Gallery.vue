<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ImageWrapper from '@/Components/Wrappers/ImageWrapper.vue';
import NavLink from '@/Components/NavLink.vue';
import { computed } from 'vue';
import LocationIcon from '@/Components/Icons/LocationIcon.vue';
import CalendarIcon from '@/Components/Icons/CalendarIcon.vue';
import BandTags from '@/Components/Tags/BandTags.vue';
import { removePostalCode } from '@/Helpers/adressFormatHelper.js';

const props = defineProps({
    gallery: {
        type: Object
    },
    url: {
        type: String
    }
});

const venueName = computed(() => {
    return props.gallery.venue?.location ?? props.gallery.venue?.name;
});

const description = 'by ' + props.gallery.user.name + '. ' + (props.gallery.description || 'Discover Armenian rock and metal bands, upcoming concerts, announcements, and photo galleries. Stay updated with the Armenian music scene on Rocker.am.');

</script>

<template>
    <GuestLayout
        :meta="{title: gallery.title+' – Armenian Rock/Metal Music, Concerts & Albums' ,image:gallery.cover_img.large , description, author:gallery.user.name,keywords: gallery.bands.map(band => band.name).join(', ')+ ', '+ gallery.title }">
        <h1>{{ gallery.title }}</h1>
        <h2 class="text-center">{{ gallery.title }}</h2>
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
            <div class="md:w-2/3 flex flex-col gap-y-4 bg-graydark2 p-4 rounded-lg text-pretty">
                <p>{{ gallery.description }}</p>
                <div v-if="venueName" class="flex gap-x-1 text-sm">
                    <LocationIcon />
                    <p>{{ removePostalCode(venueName, 80) }}</p>
                </div>
                <div class="flex gap-x-1 bg-graydark2 w-fit px-1 rounded-sm">
                    <CalendarIcon />
                    <p>{{ gallery.date }}</p>
                </div>
                <BandTags :bands="gallery.bands" />
            </div>
        </div>
        <ImageWrapper download :images="gallery.images_url" :title="gallery.title" :url />
    </GuestLayout>
</template>
