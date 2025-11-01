<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import GenresTags from '@/Components/Tags/GenresTags.vue';
import ImageWrapper from '@/Components/Wrappers/ImageWrapper.vue';
import { fullUrl, getHostname } from '@/Helpers/urlHelper.js';
import BandAlbums from '@/Components/Forms/BandAlbums.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';

defineProps({
  band: {
    type: Object,
    required: true
  }
});

const url = fullUrl();
</script>

<template>
  <GuestLayout
    :meta="{title: band.name+' – '+band.genres.map(genre =>  genre.name).join(', ')+' Armenian band' ,image: band.logo?.svg ?? band.logo?.large , description: band.info, keywords: band.name+ ' '+ band.genres.map(genre =>  genre.name).join(',')+' Armenian band ' }">
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
        <h1>{{ band.name + ' Armenian ' + band.genres.map(genre => genre.name).join(' Band from Armenia')
          }}</h1>
        <h2>{{ band.name }}</h2>
      </div>
      <SocialShare class="absolute right-0 bottom-0 bg-opacity-20 bg-graydark" :title="band.name" :url />
    </div>
    <div :class="band.name.length > 40 ? 'md:mt-20 sm:mt-32 mt-56' : 'mt-5'" class="flex flex-col gap-y-4 ">
      <h4 class="text-center mt-5">
        Genres
      </h4>
      <GenresTags class="mx-auto w-fit" :genres="band.genres" />
    </div>
    <ImageWrapper classes="flex gap-4" :images="band.images_url" :title="band.title" :url />
    <div class="md:w-5/6 mx-auto mt-8 md:p-0 p-3 whitespace-break-spaces" v-html="band.info" />
    <BandAlbums v-if="band?.albums" :albums="band.albums" />
    <EventWrapper v-if="band.events.length" class="mt-20" :events="band.events" title="Upcoming events" />
    <!--    <GalleryWrapper v-if="band?.galleries.length" :galleries="band.galleries" title="Galleries" user />-->
    <!--    <BlogWrapper v-if="band.blogs.length" :blogs="band.blogs" class="mt-20" title="Articles about the group" />-->
    <p>.......*</p>
  </GuestLayout>
</template>

<style>
.ql-video {
  width: 100%;
  height: 550px;
}
</style>
