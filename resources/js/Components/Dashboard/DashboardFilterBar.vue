<script setup>
import BotIcon from '@/Components/Icons/BotIcon.vue';
import WebSiteIcon from '@/Components/Icons/WebSiteIcon.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import { ENTITY_TYPES, SORT_OPTIONS, USER_FILTERS } from '@/Constants/dashboardConstants.js';

const props = defineProps({
  filters: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['update:type', 'update:sort', 'update:filter', 'toggle:past']);
</script>

<template>
  <div class="mt-20">
    <div class="flex flex-col items-center gap-8">
      <div class="flex flex-wrap justify-center gap-4">
        <button
          @click="emit('update:type', ENTITY_TYPES.USERS)"
          :class="['px-6 py-2 rounded-full transition-all border', filters.type === ENTITY_TYPES.USERS ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
        >
          Users
        </button>
        <button
          @click="emit('update:type', ENTITY_TYPES.BANDS)"
          :class="['px-6 py-2 rounded-full transition-all border', filters.type === ENTITY_TYPES.BANDS ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
        >
          Bands
        </button>
        <button
          @click="emit('update:type', ENTITY_TYPES.EVENTS)"
          :class="['px-6 py-2 rounded-full transition-all border', filters.type === ENTITY_TYPES.EVENTS ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
        >
          Events
        </button>
        <button
          @click="emit('update:type', ENTITY_TYPES.GALLERIES)"
          :class="['px-6 py-2 rounded-full transition-all border', filters.type === ENTITY_TYPES.GALLERIES ? 'bg-primary border-primary text-black' : 'border-white/20 hover:border-white']"
        >
          Galleries
        </button>
      </div>

      <div class="flex flex-wrap justify-center items-center gap-6 p-4 bg-white/5 rounded-2xl border border-white/10">
        <div class="flex items-center gap-2">
          <span class="text-xs opacity-50 uppercase tracking-wider">Sort:</span>
          <div class="flex bg-black rounded-lg p-1 border border-white/5">
            <button
              @click="emit('update:sort', SORT_OPTIONS.NEWEST)"
              :class="['px-3 py-1 text-sm rounded-md transition-all', filters.sort === SORT_OPTIONS.NEWEST ? 'bg-white/10 text-white' : 'text-white/40 hover:text-white']"
            >
              Newest
            </button>
            <button
              @click="emit('update:sort', SORT_OPTIONS.OLDEST)"
              :class="['px-3 py-1 text-sm rounded-md transition-all', filters.sort === SORT_OPTIONS.OLDEST ? 'bg-white/10 text-white' : 'text-white/40 hover:text-white']"
            >
              Oldest
            </button>
            <button
              v-if="filters.type === ENTITY_TYPES.USERS"
              @click="emit('update:sort', SORT_OPTIONS.ACTIVE)"
              :class="['px-3 py-1 text-sm rounded-md transition-all', filters.sort === SORT_OPTIONS.ACTIVE ? 'bg-white/10 text-white' : 'text-white/40 hover:text-white']"
            >
              Active
            </button>
          </div>
        </div>

        <div class="w-px h-6 bg-white/10"></div>

        <div v-if="filters.type === ENTITY_TYPES.EVENTS" class="flex items-center gap-3">
          <button
            @click="emit('toggle:past')"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.past ? 'bg-orange-600 border-orange-600 text-white' : 'border-white/10 text-white/60 hover:border-orange-600/50']"
          >
            <EventIcon class="w-4 h-4" />
            Archive
          </button>
        </div>

        <div v-if="filters.type === ENTITY_TYPES.USERS" class="flex flex-wrap justify-center gap-3">
          <button
            @click="emit('update:filter', USER_FILTERS.BLOCKED)"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.filter === USER_FILTERS.BLOCKED ? 'bg-red border-red text-white' : 'border-white/10 text-white/60 hover:border-red/50']"
          >
            <span class="w-2 h-2 rounded-full bg-red"></span>
            Blocked
          </button>
          <button
            @click="emit('update:filter', USER_FILTERS.BOT)"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.filter === USER_FILTERS.BOT ? 'bg-blue-600 border-blue-600 text-white' : 'border-white/10 text-white/60 hover:border-blue-600/50']"
          >
            <BotIcon class="w-4 h-4" />
            Bot
          </button>
          <button
            @click="emit('update:filter', USER_FILTERS.WEB)"
            :class="['px-4 py-1 text-sm rounded-lg transition-all border flex items-center gap-2', filters.filter === USER_FILTERS.WEB ? 'bg-green-600 border-green-600 text-white' : 'border-white/10 text-white/60 hover:border-green-600/50']"
          >
            <WebSiteIcon class="w-4 h-4" />
            Web
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-primary {
  background-color: var(--primary-color, #FFD700);
}

.border-primary {
  border-color: var(--primary-color, #FFD700);
}
</style>
