<script setup>
defineProps({
  disk: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div class="mt-8 w-full px-4 lg:px-10">
    <!-- Progress Bar (Single element with gradient to avoid 1px shifts) -->
    <div 
      class="w-full h-5 rounded-full overflow-hidden shadow-inner cursor-pointer"
      :tooltip="`Total: ${disk.total} | Used: ${disk.used} (${disk.percent_used}%) | Backups: ${disk.backups} (${disk.percent_backup}%) | Free: ${disk.free}`"
      :style="{ 
        background: `linear-gradient(to right, 
          #FF5722 0%, #FF5722 ${disk.percent_used - disk.percent_backup}%, 
          #FFC107 ${disk.percent_used - disk.percent_backup}%, #FFC107 ${disk.percent_used}%, 
          #4CAF50 ${disk.percent_used}%, #4CAF50 100%)` 
      }"
    >
    </div>

    <!-- Info Under the Line -->
    <div class="flex flex-wrap justify-between mt-3 gap-4 px-1">
      <div class="flex flex-wrap gap-4">
        <div class="flex flex-col">
          <span class="text-[10px] text-red uppercase font-bold">Occupied</span>
          <span class="text-sm font-bold text-white">{{ disk.used }} ({{ disk.percent_used }}%)</span>
        </div>
        <div class="flex flex-col border-l border-white/10 pl-4">
          <span class="text-[10px] text-yellow-400 uppercase font-bold text-yellow">Backups</span>
          <span class="text-sm font-bold text-white">{{ disk.backups }} ({{ disk.percent_backup }}%)</span>
        </div>
        <div class="flex flex-col border-l border-white/10 pl-4">
          <span class="text-[10px] text-green uppercase font-bold">Free</span>
          <span class="text-sm font-bold text-white">{{ disk.free }} ({{ disk.percent_free }}%)</span>
        </div>
      </div>
      
      <div class="flex gap-4">
        <div class="flex flex-col border-l border-white/10 pl-4 lg:border-none lg:pl-0">
          <span class="text-[10px] text-gray-400 uppercase font-bold">Project Size</span>
          <span class="text-sm font-bold text-white">{{ disk.project }}</span>
        </div>
        <div class="flex flex-col border-l border-white/10 pl-4">
          <span class="text-[10px] text-gray-400 uppercase font-bold">Total Disk</span>
          <span class="text-sm font-bold text-white">{{ disk.total }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
