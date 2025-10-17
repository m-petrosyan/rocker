<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
import WebSiteIcon from '@/Components/Icons/WebSiteIcon.vue';
import Pagination from '@/Components/Elemtns/Pagination.vue';

defineProps({
  users: {
    type: Array
  },
  statistics: {
    type: Object
  }
});
</script>

<template>
  <ProfileLayout>
    <h2 class="text-center">Statistic</h2>
    <div class="mt-10">
      <div class="flex gap-4 items-center justify-center">
        <div class="text-center bg-black p-6 rounded-lg shadow-lg">
          <p>Users</p>
          <div class="flex gap-x-4">
            <div tooltip="Website users" class="flex items-center gap-x-2">
              <WebSiteIcon />
              <h2>{{ statistics.users_web }}</h2>
            </div>

            <div tooltip="Bot users" class="flex items-center gap-x-2">
              <BotIcon />
              <h2>{{ statistics.users_bot }}</h2>
            </div>
          </div>
        </div>
        <div class="text-center bg-black p-6 rounded-lg shadow-lg">
          <p>Events</p>
          <h2>{{ statistics.events }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg shadow-lg">
          <p>Bands</p>
          <h2>{{ statistics.bands }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg shadow-lg">
          <p>Galleries</p>
          <h2>{{ statistics.galleries }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg shadow-lg">
          <p>Blogs</p>
          <h2>{{ statistics.blogs }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg shadow-lg">
          <p>PWA install</p>
          <h2>{{ statistics.pwa }}</h2>
        </div>
      </div>
    </div>
    <h2 class="mt-10 text-center">Users</h2>
    <div
      class="mt-10 grid md:grid-cols-2 md:gap-4 lg:grid-cols-4 auto-rows-[600px] md:auto-rows-[400px] gap-4">
      <NavLink :href="route('profile.show', user.username)" v-for="user in users.data"
               class="text-center flex flex-col items-center gap-4" href="">
        <div :style="{ backgroundImage: `url(${user.image?.thumb ?? '/images/user.jpg'})` }"
             class="h-48 w-48 bg-no-repeat bg-contain bg-cover rounded-full">
        </div>
        <div>
          <div>
            <p>{{ user.name }}</p>
            <p>last acivity</p>
            <small>{{ user.last_activity }}</small>
          </div>
          <div class="mt-4 flex gap-4">
            <div class="flex flex-col">
              <b>{{ user.bands_count }}</b>
              <p>Bands</p>
            </div>
            <div class="flex flex-col">
              <b>{{ user.events_count }}</b>
              <p>Events</p>
            </div>
            <div class="flex flex-col">
              <b>{{ user.galleries_count }}</b>
              <p>Gallery</p>
            </div>
            <div class="flex flex-col">
              <b>{{ user.blogs_count }}</b>
              <p>Blog</p>
            </div>
          </div>
        </div>
      </NavLink>
    </div>
    <Pagination :links="users?.links" />
  </ProfileLayout>
</template>
