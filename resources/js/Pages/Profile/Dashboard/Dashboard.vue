<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
import WebSiteIcon from '@/Components/Icons/WebSiteIcon.vue';
import Pagination from '@/Components/Elemtns/Pagination.vue';
import { formatDateTime } from '@/Helpers/dateFormatHelper.js';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import StatisticsChart from '@/Components/Elements/StatisticsChart.vue';

defineProps({
  users: {
    type: Object
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
      <div class="flex flex-wrap lg:flex-nowrap gap-4 items-center justify-center">
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Users</p>
          <div class="flex flex-wrap gap-x-4">
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
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Events</p>
          <div class="flex">
            <h2 tooltip="all" class="opacity-30">{{ statistics.events }}</h2>
            <h2>/</h2>
            <h2 tooltip="active">{{ statistics.events_active }}</h2>
          </div>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Bands</p>
          <h2>{{ statistics.bands }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>Galleries</p>
          <h2>{{ statistics.galleries }}</h2>
        </div>
        <div v-if="statistics.blogs" class="text-center bg-black p-6 rounded-lg">
          <p>Blogs</p>
          <h2>{{ statistics.blogs }}</h2>
        </div>
        <div class="text-center bg-black p-6 rounded-lg">
          <p>PWA install</p>
          <h2>{{ statistics.pwa }}</h2>
        </div>
      </div>

      <div class="mt-10 grid lg:grid-cols-2 gap-8" v-if="statistics.charts">
        <StatisticsChart
          title="Active users"
          :labels="statistics.charts.users.labels"
          :data="statistics.charts.users.data"
          type="line"
          color="#FF5722"
        />
        <StatisticsChart
          title="New events"
          :labels="statistics.charts.events.labels"
          :data="statistics.charts.events.data"
          type="bar"
          color="#4CAF50"
        />
      </div>
    </div>
    <h2 class="mt-10 text-center">Users</h2>
    <div
      class="mt-10 grid md:grid-cols-2 gap-x-10 gap-y-20 lg:grid-cols-4 ">
      <NavLink :href="route('profile.show', user.username)" v-for="user in users.data"
               class="text-center flex flex-col items-center gap-4" href="">
        <div :style="{ backgroundImage: `url(${user.image?.thumb ?? '/images/user.jpg'})` }"
             class="relative h-48 w-48 bg-no-repeat bg-contain bg-cover rounded-full overflow-hidden">
          <div class="absolute w-full bottom-0 p-2 flex gap-4 justify-center items-center bg-blackTransparent">
            <BotIcon v-if="user.chat_count" />
            <WebSiteIcon v-if="user.email" />
          </div>
        </div>
        <div>
          <div v-if="user.is_blocked" class="bg-red text-white">Blocked</div>
          <div>
            <p>{{ user.name }}</p>
            <small
              tooltip="last acivity">{{ user.last_activity ? formatDateTime(user.last_activity, 'DD/MM/YY HH:mm') : 'No activity yet'
              }}</small>
          </div>
          <div class="mt-2 flex gap-4">
            <div class="flex flex-col">
              <b>{{ user.events_count }}</b>
              <EventIcon />
            </div>
            <div class="flex flex-col">
              <b>{{ user.bands_count }}</b>
              <BandIcon />
            </div>
            <div class="flex flex-col ">
              <b>{{ user.galleries_count }}</b>
              <GalleryIcon />
            </div>
            <!--            <div class="flex flex-col">-->
            <!--              <b>{{ user.blogs_count }}</b>-->
            <!--              <p>Blog</p>-->
            <!--            </div>-->
          </div>
        </div>
      </NavLink>
    </div>
    <Pagination :links="users?.links" />
  </ProfileLayout>
</template>
