<script setup>
import NavLink from '@/Components/NavLink.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
import WebSiteIcon from '@/Components/Icons/WebSiteIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import { formatDateTime } from '@/Helpers/dateFormatHelper.js';
import { ENTITY_TYPES } from '@/Constants/dashboardConstants.js';

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  type: {
    type: String,
    required: true
  },
  imageUrl: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  link: {
    type: String,
    required: true
  }
});
</script>

<template>
  <NavLink :href="link" class="text-center group flex flex-col items-center gap-4">
    <div 
      :style="{ backgroundImage: `url(${imageUrl})` }"
      class="relative h-48 w-48 bg-no-repeat bg-contain bg-cover rounded-full overflow-hidden transition-transform group-hover:scale-105 shadow-2xl"
    >
      <div v-if="type === ENTITY_TYPES.USERS" class="absolute w-full bottom-0">
        <div class="flex justify-end px-4">
          <img 
            class="h-6" 
            v-if="item?.settings?.country && item?.settings?.country !== 'all'"
            :src="`/icons/${item?.settings?.country}.png`" 
            alt="flag"
          >
          <div v-else-if="item?.settings?.country === 'all'" class="flex imems-center gap-x-2">
            <img class="h-6" :src="`/icons/am.png`" alt="flag">
            <img class="h-6" :src="`/icons/ge.png`" alt="flag">
          </div>
        </div>
        <div class="flex gap-4 justify-center items-center bg-blackTransparent p-2">
          <BotIcon v-if="item.chat_count" />
          <WebSiteIcon v-if="item.email" />
        </div>
      </div>
    </div>

    <div>
      <div 
        v-if="type === ENTITY_TYPES.USERS && item.is_blocked"
        class="bg-red text-white text-xs px-2 py-0.5 rounded mb-1 inline-block uppercase font-bold tracking-tighter"
      >
        Blocked
      </div>
      <div>
        <p class="font-bold border-b border-transparent group-hover:border-primary transition-colors inline-block text-lg">
          {{ title }}
        </p>
        <div v-if="type === ENTITY_TYPES.USERS">
          <small
            tooltip="last acivity"
            :class="['block mt-1', {'opacity-75': !item.last_activity}]"
          >
            {{ item.last_activity ? formatDateTime(item.last_activity, 'DD/MM/YY HH:mm') : formatDateTime(item.created_at, 'DD/MM/YY HH:mm') }}
          </small>
        </div>
        <div v-else-if="item.date || item.start_date" class="block mt-1">
          <small class="opacity-75">{{ formatDateTime(item.date || item.start_date, 'DD/MM/YY') }}</small>
        </div>
      </div>

      <div v-if="type === ENTITY_TYPES.USERS" class="mt-4 flex gap-6 justify-center">
        <div class="flex flex-col items-center gap-1 group/icon" tooltip="Events">
          <b class="text-sm">{{ item.events_count }}</b>
          <EventIcon class="w-4 h-4 opacity-30 group-hover/icon:opacity-100 transition-opacity" />
        </div>
        <div class="flex flex-col items-center gap-1 group/icon" tooltip="Bands">
          <b class="text-sm">{{ item.bands_count }}</b>
          <BandIcon class="w-4 h-4 opacity-30 group-hover/icon:opacity-100 transition-opacity" />
        </div>
        <div class="flex flex-col items-center gap-1 group/icon" tooltip="Galleries">
          <b class="text-sm">{{ item.galleries_count }}</b>
          <GalleryIcon class="w-4 h-4 opacity-30 group-hover/icon:opacity-100 transition-opacity" />
        </div>
      </div>
    </div>
  </NavLink>
</template>

<style scoped>
.border-primary {
  border-color: var(--primary-color, #FFD700);
}
</style>
