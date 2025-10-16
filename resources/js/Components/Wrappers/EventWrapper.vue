<script setup>
import moment from 'moment-timezone';
import NavLink from '@/Components/NavLink.vue';
import NotifyBotIcon from '@/Components/Icons/NotifyBotIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import { router } from '@inertiajs/vue3';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import { removePostalCode } from '@/Helpers/adressFormatHelper.js';

defineProps({
  events: {
    type: Object,
    required: true
  },
  add: {
    type: Boolean,
    default: false
  },
  more: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  owner: {
    type: Boolean,
    default: false
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  profile: {
    type: Boolean,
    default: false
  },
  request: {
    type: Boolean,
    default: false
  }
});

const deleteEvent = (id) => {
  if (confirm('Are you sure you want to delete this event?')) {
    router.delete(route('profile.events.destroy', id), {
      preserveState: false,
      preserveScroll: true
    });
  }
};
</script>
<template>
  <div class="relative">
    <h3 v-if="title" class="text-center">{{ title }}</h3>
    <div class="mt-10 grid gap-y-10 md:grid-cols-2 md:gap-4 lg:grid-cols-4 auto-rows-[600px] md:auto-rows-[400px]">
      <div
        v-for="event in events"
        :key="event.id"
        class="h-[600px] md:h-[400px]"
      >
        <NavLink
          :href="route(request ? 'profile.event.requests' : 'events.show', event.id)"
          class="relative h-full w-full block"
          :style="{ backgroundImage: `url(${event.poster.thumb})` }"
        >
          <div v-if="owner && (['pending','rejected'].includes(event.status_name))"
               class="absolute w-full text-center h-full content-center bg-blackTransparent2"
               :class="event.status_name === 'pending' ? 'z-20' : 'z-30'">
            <div class="bg-blackTransparent2">
              <h2 class="capitalize"
                  :class="event.status_name === 'pending' ? 'text-green' : 'text-red'">
                {{ event.status_name }}
              </h2>
              <p v-if="event.status_text" class="p-2 mt-2">Reason:
                {{ event.status_text }}</p>
            </div>
          </div>
          <div class="absolute inset-0 z-0 brightness-50 backdrop-blur-md"></div>
          <img
            :src="event.poster.thumb"
            :alt="event.title"
            class="absolute z-10 h-full w-full object-contain object-center"
          />
          <div
            class="absolute  left-0 z-20 flex flex-col w-full h-full flex justify-between">
            <div class="h-28 w-full flex  justify-between">
              <div class="w-28 h-full flex flex-col items-center justify-center bg-orange text-xl">
                <p class="text-4xl font-bold">
                  {{ moment(event.start_date_short, 'DD.MM.YY').format('D').toUpperCase() }}
                </p>
                <p>
                  {{ moment(event.start_date_short, 'DD.MM.YY').format('MMMM').toUpperCase() }}
                </p>
                <small>{{ event.start_time }}</small>
              </div>
              <div v-if="(owner || isAdmin ) && event.status_name === 'accepted'">
                <div class="flex flex-col gap-y-2 bg-blackTransparent2 p-2">
                  <div v-if="event.notify_count" tooltip="Sent by bot"
                       class="flex items-center gap-2">
                    <NotifyBotIcon />
                    {{ event.notify_count }}
                  </div>
                  <div v-if="event.allViews" tooltip="Views in rocker"
                       class="flex items-center gap-2">
                    <EyesIcon />
                    {{ event.allViews }}
                  </div>
                </div>
              </div>
            </div>
            <div
              class="flex flex-col h-20 w-full z-20 items-center justify-between bg-gradient-to-t from-black to-transparent">
              <div
                class="absolute pb-2 flex flex-col justify-end h-48 bottom-0 z-20 w-full bg-gradient-to-t from-black to-transparent ">
                <p class="text-center text-xl font-bold">{{ event.title }}</p>
                <p class="text-gray-300 text-center">{{ removePostalCode(event.location, 30) }}</p>
              </div>
              <div v-if="(owner || isAdmin) && profile"
                   class="flex w-full items-center justify-between z-20">
                <NavLink tooltip="Edit" :href="route('profile.events.edit', event.id)">
                  <EditIcon />
                </NavLink>
                <button tooltip="Delete" @click.prevent="deleteEvent(event.id)"
                        class="text-red-500 hover:text-red-700">
                  <DeleteIcon />
                </button>
              </div>
            </div>
          </div>
        </NavLink>
      </div>
      <NavLink
        v-if="add  && !request"
        :href="route('profile.events.create')"
        class="flex h-[600px] items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2 md:h-[400px]"
      >
        <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
          <h2 class="text-3xl">+</h2>
          <h3>Add event</h3>
        </div>
      </NavLink>
    </div>
    <div v-if="more"
         class="col-span-full text-center py-4">
      <NavLink :href="route('events.index')"
               label="Events list"
               class="text-orange font-bold">
        Explore upcoming concerts
      </NavLink>
    </div>
  </div>
</template>
