<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import GenresTags from '@/Components/Tags/GenresTags.vue';
import ImageWrapper from '@/Components/Wrappers/ImageWrapper.vue';
import { getHostname } from '@/Helpers/urlHelper.js';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import BandAlbums from '@/Components/Forms/BandAlbums.vue';

defineProps({
    band: {
        type: Object,
        required: true
    },
    url: {
        type: String,
        required: true
    },
    events: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <GuestLayout
        :meta="{title: band.name ,image: band.logo?.svg ?? band.logo?.large , description: band.info, keywords: band.name}">
        <div class="lg:h-96 h-64  relative">
            <div v-if="band.links.length"
                 class="absolute flex md:flex-col gap-x-5 right-0 w-fit top-0 bg-opacity-20 bg-graydark p-3">
                <a v-for="link in band.links" :key="link.id" :href="link.url"
                   target="_blank">{{ getHostname(link.url)
                    }}</a>
            </div>
            <img
                :src="band.cover?.large"
                class="w-full h-full object-cover"
                :alt="band.name"
                :style="band.cover_position ? `object-position: ${band.cover_position.x}% ${band.cover_position.y}%` : ''"
            />
            <img :src="band.logo?.svg ?? band.logo?.thumb"
                 class="z-10 lg:w-48 md:w-32 w-2/12 absolute lg:bottom-0 lg:-translate-x-0 lg:left-0 bottom-5 left-1/2  -translate-x-1/2"
                 :alt="band.name">
            <div class="absolute h-1/6 left-1/2 lg:w-fit w-full -translate-x-1/2 text-center bottom-0 bg-graydark p-5">
                <h1>{{ band.name }}</h1>
            </div>
            <SocialShare class="absolute right-0 bottom-0 bg-opacity-20 bg-graydark" :title="band.name" :url />
        </div>
        <div :class="band.name.length > 40 ? 'md:mt-20 sm:mt-32 mt-56' : 'mt-5'" class="flex flex-col gap-y-4 ">
            <h3 class="text-center mt-5">
                Genres
            </h3>
            <GenresTags class="mx-auto w-fit" :genres="band.genres" />
        </div>
        <ImageWrapper classes="flex gap-4" :images="band.images_url" :title="band.title" :url />
        <div class="md:w-5/6 mx-auto mt-8 md:p-0 p-3 whitespace-break-spaces" v-html="band.info" />
        <div v-if="band.albums.length" class="mt-20">
            <h2 class="text-center">Albums</h2>
            <div class="mt-2 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 grid gap-4 mt-4">
                <BandAlbums
                    v-for="(album, index) in band.albums"
                    :key="`album-${album.id || 'new'}-${index}`"
                    :album="album"
                    :index="index"
                />
            </div>
        </div>
        <EventWrapper v-if="events.data.length" class="mt-20" :events="events.data" title="Upcoming events" />
        <GalleryWrapper v-if="band.galleries.length" :galleries="band.galleries" title="Galleries" user />
    </GuestLayout>
</template>

<style>
.ql-video {
    width: 100%;
    height: 550px;
}
</style>
