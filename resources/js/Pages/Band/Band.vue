<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import Tags from '@/Components/Elements/Tags.vue';

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
</script>

<template>
    <GuestLayout
        :meta="{title: band.name ,image:band.logo?.large , description: band.info, keywords: band.name}">
        <div class="h-96 relative">
            <img :src="band.cover?.large" class="w-full h-full object-cover" :alt="band.name">
            <img :src="band.logo?.thumb" class="w-24 h-24 absolute left-0 bottom-0" :alt="band.name">
            <div class="absolute h-1/6 left-1/2 w-fit -translate-x-1/2 text-center bottom-0 bg-graydark p-5">
                <h1>{{ band.name }}</h1>
            </div>
            <SocialShare class="absolute right-0 bottom-0" :title="band.name" :url />
        </div>
        <div class="flex flex-col gap-y-4 mt-5">
            <h3 class="text-center mt-5">
                Genres
            </h3>
            <Tags class="mx-auto w-fit" :items="band.genres" />
        </div>
        <div class="md:w-5/6 mx-auto mt-8" v-html="band.info" />
    </GuestLayout>
</template>

<style>
.ql-video {
    width: 100%;
    height: 550px;
}
</style>
