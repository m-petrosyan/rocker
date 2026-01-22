<script setup>
defineProps({
  countries: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div class="flex items-center gap-x-4 border-l border-white/10 pl-4 ml-2">
    <div tooltip="Armenia" class="flex items-center gap-x-1">
      <img src="/icons/am.png" class="h-4" alt="am">
      <h3 class="text-lg font-bold">{{ countries.am }}</h3>
    </div>
    <div tooltip="Georgia" class="flex items-center gap-x-1">
      <img src="/icons/ge.png" class="h-4" alt="ge">
      <h3 class="text-lg font-bold">{{ countries.ge }}</h3>
    </div>
    <div tooltip="All countries" class="flex items-center gap-x-1">
      <span class="text-xs opacity-50 uppercase font-bold">All</span>
      <h3 class="text-lg font-bold">{{ countries.all }}</h3>
    </div>
  </div>
</template>
