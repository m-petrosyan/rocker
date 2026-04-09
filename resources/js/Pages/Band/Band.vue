<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import GenresTags from '@/Components/Tags/GenresTags.vue';
import ImageWrapper from '@/Components/Wrappers/ImageWrapper.vue';
import { fullUrl, getHostname } from '@/Helpers/urlHelper.js';
import BandAlbums from '@/Components/Forms/BandAlbums.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import BlogWrapper from '@/Components/Wrappers/BlogWrapper.vue';

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
    :meta="{
            title:
                band.name +
                ' – ' +
                band.genres.map((genre) => genre.name).join(', ') +
                ' Armenian band',
            image: band.logo?.svg ?? band.logo?.large,
            description: band.info,
            keywords:
                band.name +
                ' ' +
                band.genres.map((genre) => genre.name).join(',') +
                ' Armenian band ',
        }"
  >
    <div class="relative h-64 lg:h-96">
      <div
        v-if="band.links.length"
        class="absolute right-0 top-0 flex w-fit gap-x-5 bg-graydark bg-opacity-20 p-3 md:flex-col"
      >
        <a
          v-for="link in band.links"
          :key="link.id"
          :href="link.url"
          target="_blank"
        >{{ getHostname(link.url) }}</a
        >
      </div>
      <img
        :src="band.cover?.large"
        class="h-full w-full object-cover"
        :alt="band.name"
        :style="
                    band.cover_position
                        ? `object-position: ${band.cover_position.x}% ${band.cover_position.y}%`
                        : ''
                "
        loading="eager"
        fetchpriority="high"
      />
      <img
        :src="band.logo?.svg ?? band.logo?.thumb"
        class="absolute bottom-5 left-1/2 z-10 w-2/12 -translate-x-1/2 md:w-32 lg:bottom-0 lg:left-0 lg:w-48 lg:-translate-x-0"
        :alt="band.name"
        loading="eager"
        fetchpriority="high"
      />
      <div
        class="absolute bottom-0 left-1/2 h-1/6 w-full -translate-x-1/2 bg-graydark p-5 text-center lg:w-fit"
      >
        <h1>
          {{
            band.name +
            ' Armenian ' +
            band.genres
              .map((genre) => genre.name)
              .join(' Band from Armenia')
          }}
        </h1>
        <h2>{{ band.name }}</h2>
      </div>
      <SocialShare
        class="absolute bottom-0 right-0 bg-graydark bg-opacity-20"
        :title="band.name"
        :url
      />
    </div>
    <div
      :class="band.name.length > 40 ? 'mt-56 sm:mt-32 md:mt-20' : 'mt-5'"
      class="flex flex-col gap-y-4"
    >
      <h4 class="mt-5 text-center">Genres</h4>
      <GenresTags class="mx-auto w-fit" :genres="band.genres" />
    </div>
    <ImageWrapper
      classes="flex gap-4"
      :images="band.images_url"
      :title="band.name"
      :url
    />
    <div
      class="mx-auto mt-8 whitespace-break-spaces p-3 md:w-5/6 md:p-0"
      v-html="band.info"
    />
    <BandAlbums v-if="band.albums" :albums="band.albums" />
    <EventWrapper
      v-if="band.events.length"
      class="mt-20"
      :events="band.events"
      title="Upcoming events"
    />
    <GalleryWrapper
      v-if="band.galleries.length"
      :galleries="band.galleries"
      title="Galleries"
      user
    />
    <BlogWrapper
      v-if="band.blogs.length"
      :blogs="band.blogs"
      class="mt-20"
      title="Articles about the group"
    />
  </GuestLayout>
</template>

<style>
.ql-video {
  width: 100%;
  height: 550px;
}
</style>
