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
import DiskUsage from '@/Components/Elements/DiskUsage.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  users: {
    type: Object
  },
  statistics: {
    type: Object
  },
  filters: {
    type: Object,
    default: () => ({ type: 'users', filter: null })
  }
});

const setType = (type) => {
  router.get(route('profile.dashboard'), { type }, {
    preserveState: true,
    preserveScroll: true,
    only: ['users', 'filters']
  });
};

const setFilter = (filter) => {
  router.get(route('profile.dashboard'), {
    type: props.filters.type,
    filter: props.filters.filter === filter ? null : filter
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['users', 'filters']
  });
};

const getImageUrl = (item) => {
  if (props.filters.type === 'users') return item.image?.thumb ?? '/images/user.jpg';
  if (props.filters.type === 'bands') return item.logo?.thumb ?? '/images/band.jpg'; // Предполагаем структуру Band
  if (props.filters.type === 'events') return item.poster?.thumb ?? '/images/event.jpg'; // Предполагаем структуру Event
  if (props.filters.type === 'galleries') return item.cover_img?.thumb ?? '/images/gallery.jpg'; // Предполагаем структуру Gallery
  return '/images/placeholder.jpg';
};

const getTitle = (item) => {
  if (props.filters.type === 'users') return item.name;
  if (props.filters.type === 'bands') return item.name;
  if (props.filters.type === 'events') return item.title;
  if (props.filters.type === 'galleries') return item.title || item.description;
  return 'Unknown';
};

const getLink = (item) => {
  if (props.filters.type === 'users') return route('profile.show', item.username);
  if (props.filters.type === 'bands') return route('bands.show', item.slug);
  if (props.filters.type === 'events') return route('events.show', item.id);
  if (props.filters.type === 'galleries') return route('galleries.show', item.id);
  return '#';
};
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

      <DiskUsage :disk="statistics.disk" />

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

    <!-- Фильтры -->
    <div class="mt-20">
      <div class="flex flex-col items-center gap-6">
        <!-- Типы сущностей -->
        <div class="flex flex-wrap justify-center gap-4">
          <button
            @click="setType('users')"
            :class="['px-6 py-2 rounded-full transition-all border', filters.type === 'users' ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
          >
            Users
          </button>
          <button
            @click="setType('bands')"
            :class="['px-6 py-2 rounded-full transition-all border', filters.type === 'bands' ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
          >
            Bands
          </button>
          <button
            @click="setType('events')"
            :class="['px-6 py-2 rounded-full transition-all border', filters.type === 'events' ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
          >
            Events
          </button>
          <button
            @click="setType('galleries')"
            :class="['px-6 py-2 rounded-full transition-all border', filters.type === 'galleries' ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
          >
            Galleries
          </button>
        </div>

        <!-- Под-фильтры для пользователей -->
        <div v-if="filters.type === 'users'" class="flex flex-wrap justify-center gap-3">
          <button
            @click="setFilter('blocked')"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.filter === 'blocked' ? 'bg-red border-red' : 'border-white/10 hover:border-red/50']"
          >
            <span class="w-2 h-2 rounded-full bg-red"></span>
            Blocked
          </button>
          <button
            @click="setFilter('bot')"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.filter === 'bot' ? 'bg-blue-600 border-blue-600' : 'border-white/10 hover:border-blue-600/50']"
          >
            <BotIcon class="w-4 h-4" />
            Only Bot
          </button>
          <button
            @click="setFilter('web')"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.filter === 'web' ? 'bg-green-600 border-green-600' : 'border-white/10 hover:border-green-600/50']"
          >
            <WebSiteIcon class="w-4 h-4" />
            Only Web
          </button>
        </div>
      </div>
    </div>

    <div
      class="mt-10 grid md:grid-cols-2 gap-x-10 gap-y-20 lg:grid-cols-4 ">
      <NavLink :href="getLink(item)" v-for="item in users.data"
               class="text-center group flex flex-col items-center gap-4">
        <div :style="{ backgroundImage: `url(${getImageUrl(item)})` }"
             class="relative h-48 w-48 bg-no-repeat bg-contain bg-cover rounded-full overflow-hidden transition-transform group-hover:scale-105">
          <div v-if="filters.type === 'users'" class="absolute w-full bottom-0 p-2 flex gap-4 justify-center items-center bg-blackTransparent">
            <BotIcon v-if="item.chat_count" />
            <WebSiteIcon v-if="item.email" />
          </div>
        </div>
        <div>
          <div v-if="filters.type === 'users' && item.is_blocked" class="bg-red text-white text-xs px-2 py-0.5 rounded mb-1 inline-block">Blocked</div>
          <div>
            <p class="font-bold border-b border-transparent group-hover:border-primary transition-colors inline-block">{{ getTitle(item) }}</p>
            <div v-if="filters.type === 'users'">
              <small
                tooltip="last acivity"
                :class="{'opacity-75': !item.last_activity}">
                {{ item.last_activity ? formatDateTime(item.last_activity, 'DD/MM/YY HH:mm') : formatDateTime(item.created_at, 'DD/MM/YY HH:mm')
                }}</small>
            </div>
            <div v-else-if="item.date || item.start_date">
                 <small class="opacity-75">{{ formatDateTime(item.date || item.start_date, 'DD/MM/YY') }}</small>
            </div>
          </div>
          <div v-if="filters.type === 'users'" class="mt-2 flex gap-4">
            <div class="flex flex-col items-center">
              <b>{{ item.events_count }}</b>
              <EventIcon class="w-4 h-4 opacity-50" />
            </div>
            <div class="flex flex-col items-center">
              <b>{{ item.bands_count }}</b>
              <BandIcon class="w-4 h-4 opacity-50" />
            </div>
            <div class="flex flex-col items-center">
              <b>{{ item.galleries_count }}</b>
              <GalleryIcon class="w-4 h-4 opacity-50" />
            </div>
          </div>
        </div>
      </NavLink>
    </div>
    <div class="mt-20">
        <Pagination :links="users?.links" />
    </div>
  </ProfileLayout>
</template>

<style scoped>
.bg-primary {
  background-color: var(--primary-color, #FFD700);
}
.border-primary {
  border-color: var(--primary-color, #FFD700);
}
.text-primary {
  color: var(--primary-color, #FFD700);
}
</style>
