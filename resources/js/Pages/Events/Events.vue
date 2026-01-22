<script setup>
import { computed, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import Pagination from '@/Components/Elemtns/Pagination.vue';
import NavLink from '@/Components/NavLink.vue';
import { isSSR } from '@/Helpers/ssrHelper.js';
import { isWebApp } from '@/Helpers/setAppUser.js';
import SettingsIcon from '@/Components/Icons/SettingsIcon.vue';
import EventFilters from '@/Components/Elements/EventFilters.vue';

const props = defineProps({
  events: {
    type: Object
  },
  isPast: {
    type: Boolean,
    default: false
  },
  auth: {
    type: Object
  }
});

const eventRequest = !isSSR ? route().current('profile.events.requests') : null;

const webApp = isWebApp();

const showFilters = ref(false);

const currentFilters = computed(() => {
  if (isSSR) return {};
  const params = new URLSearchParams(window.location.search);
  return {
    from: params.get('from'),
    to: params.get('to'),
    country: params.get('country')
  };
});

const hasActiveFilters = computed(() => {
  return currentFilters.value.from || currentFilters.value.to || (currentFilters.value.country && currentFilters.value.country !== 'all');
});
</script>

<template>
  <GuestLayout
    :meta="{ title: 'Events - Armenian Rock & Metal Concerts & Events',
        keywords: 'armenian rock events, armenian metal events, armenian rock concerts, armenian metal concerts, armenian rock festivals, armenian metal festivals, yerevan rock events, yerevan metal concerts, yerevan rock concerts, yerevan metal concerts, armenian live rock, armenian live metal, armenian concert events, armenian rock events calendar, armenian metal events calendar, armenian music events, armenian underground concerts, armenian underground music events, armenian concert announcements, armenian rock show announcements, armenian metal show announcements, armenian rock gigs, armenian metal gigs, armenian live music shows, yerevan concert events' + events.data.map(event => event.title).join(', '),
        description: 'Discover upcoming and past Armenian rock and metal concerts and events in Yerevan and beyond. Stay updated with the latest live music happenings in the Armenian rock and metal scene.' }">
    <template #header> Events</template>
    <template v-if="!webApp" #h1>
      {{ isPast
      ? 'Past Armenian Rock & Metal Concerts and Events Archive'
      : 'Upcoming Armenian Rock & Metal Concerts and Events'
      }}
    </template>
    <p v-if="!eventRequest && !webApp" class="text-gray text-pretty">
      Armenian rock and metal concerts are a vibrant part of the local scene.
      This page features
      {{ isPast ? 'past Armenian rock & metal concerts and events' : 'upcoming Armenian rock & metal concerts and events'
      }}
      in Yerevan and beyond, collected in one place for quick discovery.
    </p>
    <div class="flex justify-between items-center px-4 md:px-0 mt-4 mx-auto w-fit">
      <button
        v-if="!eventRequest"
        @click="showFilters = !showFilters"
        :class="[showFilters ? 'bg-white/20' : 'bg-white/10 hover:bg-white/20']"
        class="flex items-center gap-x-2 text-sm px-4 py-2 rounded-xl transition-all active:scale-95 border border-white/5"
      >
        <SettingsIcon class="h-4 w-4" />
        <span>Filters</span>
        <span v-if="hasActiveFilters" class="w-2 h-2 bg-red rounded-full"></span>
      </button>
    </div>
    <EventFilters
      :show="showFilters"
      :auth="auth"
      :initial-filters="currentFilters"
      @close="showFilters = false"
    />
    <h3 v-if="!webApp && eventRequest" class="text-red text-center">
      <span v-if="events.data.length">
              Here are the event requests.
      </span>
      <span v-else>
              No event requests yet.
      </span>
    </h3>
    <EventWrapper :events="events.data" v-bind:add="!isPast" :request="eventRequest" />
    <template v-if="!eventRequest && !webApp">
      <NavLink
        v-if="!isPast"
        :href="route('events.past',  { page: 1 })"
        class="flex flex-col items-center p-4">
        Past events
      </NavLink>
      <Pagination v-else :links="events.links" />
      <p class="px-4 pt-10 text-gray text-pretty">
        Check listings for dates, venues, and band details.
        Use this guide to revisit standout performances in the archive or plan your next night out at a live show.
        Stay tuned so you never miss key Armenian rock and metal events.
      </p>
    </template>
  </GuestLayout>
</template>
