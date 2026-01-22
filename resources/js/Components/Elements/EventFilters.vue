<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import RangeDatePicker from '@/Components/Forms/RangeDatePicker.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  auth: {
    type: Object
  },
  initialFilters: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['close']);

const dateRange = ref(
    props.initialFilters.from && props.initialFilters.to
        ? [new Date(props.initialFilters.from), new Date(props.initialFilters.to)]
        : null
);
const selectedCountry = ref(props.initialFilters.country || 'all');

const applyFilters = () => {
  const params = {};
  if (dateRange.value && dateRange.value.length === 2 && dateRange.value[0] && dateRange.value[1]) {
    params.from = dateRange.value[0].toISOString().split('T')[0];
    params.to = dateRange.value[1].toISOString().split('T')[0];
  } else {
      // If range is cleared or incomplete, we don't send from/to
  }
  
  if (selectedCountry.value && selectedCountry.value !== 'all') {
    params.country = selectedCountry.value;
  }

  router.get(route(route().current()), params, { preserveState: true, preserveScroll: true });
};

const debouncedApply = useDebounceFn(applyFilters, 600);

watch([dateRange, selectedCountry], () => {
    debouncedApply();
});

const resetFilters = () => {
  dateRange.value = null;
  selectedCountry.value = 'all';
  router.get(route(route().current()), {}, { preserveState: true, preserveScroll: true });
  emit('close');
};
</script>

<template>
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 -translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-2"
  >
    <div v-if="show" class="mb-8 relative py-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <div class="space-y-3">
          <label class="block text-[9px] font-bold text-gray/40 uppercase tracking-[0.2em] ml-2">Select Date Range</label>
          <div class="p-2">
            <RangeDatePicker v-model="dateRange" />
          </div>
        </div>

        <div class="flex flex-col justify-between h-full space-y-6">
          <div class="space-y-3">
            <label class="block text-[9px] font-bold text-gray/40 uppercase tracking-[0.2em] ml-2">Filter by Country</label>
            <div class="flex gap-x-4 ml-2">
              <button
                @click="selectedCountry = 'all'"
                :class="[selectedCountry === 'all' ? 'text-red border-red' : 'text-gray/40 border-white/5 hover:text-white']"
                class="flex items-center justify-center h-8 px-3 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all border bg-white/5"
              >
                All
              </button>
              <button
                @click="selectedCountry = 'am'"
                :class="[selectedCountry === 'am' ? 'border-red ring-1 ring-red/30' : 'border-white/10 opacity-40 hover:opacity-100']"
                class="h-8 w-12 rounded-lg overflow-hidden border transition-all active:scale-95 flex items-center justify-center bg-black/20"
              >
                <img src="/icons/am.png" class="h-4 object-contain" alt="AM">
              </button>
              <button
                @click="selectedCountry = 'ge'"
                :class="[selectedCountry === 'ge' ? 'border-red ring-1 ring-red/30' : 'border-white/10 opacity-40 hover:opacity-100']"
                class="h-8 w-12 rounded-lg overflow-hidden border transition-all active:scale-95 flex items-center justify-center bg-black/20"
              >
                <img src="/icons/ge.png" class="h-4 object-contain" alt="GE">
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between gap-x-4 pt-4 mt-auto">
            <button
              @click="resetFilters"
              class="text-[9px] font-bold uppercase tracking-[0.2em] text-gray/30 hover:text-red transition-colors ml-2"
            >
              Reset All
            </button>
            
            <div class="text-[9px] text-gray/20 uppercase tracking-widest italic ml-auto pr-2">
              Changes apply automatically
            </div>
          </div>
        </div>
      </div>
      <div class="mt-8 h-[1px] bg-gradient-to-r from-transparent via-white/5 to-transparent"></div>
    </div>
  </transition>
</template>
