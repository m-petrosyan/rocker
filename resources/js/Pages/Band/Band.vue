<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import GenresTags from '@/Components/Tags/GenresTags.vue';

defineProps({
    band: {
        type: Object,
        required: true
    },
    url: {
        type: String,
        required: true
    }
});

const getHostname = (url) => {
    try {
        const hostname = new URL(url).hostname;
        return hostname.startsWith('www.') ? hostname.slice(4) : hostname;
    } catch (e) {
        return url;
    }
};
</script>

<template>
    <GuestLayout
        :meta="{title: band.name ,image:band.logo?.large , description: band.info, keywords: band.name}">
        <div class="lg:h-96 h-64  relative">
            <div v-if="band.links.length"
                 class="absolute flex md:flex-col gap-x-5 right-0 w-fit top-0 bg-opacity-20 bg-graydark p-3">
                <a v-for="link in band.links" :key="link.id" :href="link.url"
                   target="_blank">{{ getHostname(link.url)
                    }}</a>
            </div>
            <img :src="band.cover?.large" class="w-full h-full lg:object-cover object-contain" :alt="band.name">
            <img :src="band.logo?.thumb"
                 class="z-10 lg:w-48 md:w-32 w-2/12 absolute lg:bottom-0 lg:-translate-x-0 lg:left-0 bottom-5 left-1/2  -translate-x-1/2"
                 :alt="band.name">
            <div class="absolute h-1/6 left-1/2 lg:w-fit w-full -translate-x-1/2 text-center bottom-0 bg-graydark p-5">
                <h1>{{ band.name }}</h1>
            </div>
            <SocialShare class="absolute right-0 bottom-0 bg-opacity-20 bg-graydark" :title="band.name" :url />
        </div>
        <div class="flex flex-col gap-y-4 mt-5">
            <h3 class="text-center mt-5">
                Genres
            </h3>
            <GenresTags class="mx-auto w-fit" :genres="band.genres" />
        </div>
        <div class="md:w-5/6 mx-auto mt-8 md:p-0 p-3" v-html="band.info" />
        <h3 class="text-center mt-10">Galleries</h3>
        <GalleryWrapper :galleries="band.galleries" user />
    </GuestLayout>
</template>

<style>
.ql-video {
    width: 100%;
    height: 550px;
}
</style>
