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
    <!-- Progress Bar -->
    <div 
      class="w-full h-5 bg-green rounded-full overflow-hidden flex shadow-inner cursor-pointer"
      :tooltip="`Total: ${disk.total} | Used: ${disk.used} (${disk.percent_used}%) | Free: ${disk.free}`"
    >
      <div 
        class="h-full bg-red transition-all duration-500 flex items-center justify-center text-[8px] text-white font-bold" 
        :style="{ width: `${disk.percent_used}%`, minWidth: disk.percent_used > 0 ? '4px' : '0' }"
      >
      </div>
    </div>

    <!-- Info Under the Line -->
    <div class="flex flex-wrap justify-between mt-3 gap-4 px-1">
      <div class="flex gap-4">
        <div class="flex flex-col">
          <span class="text-[10px] text-red uppercase font-bold">Occupied</span>
          <span class="text-sm font-bold text-white">{{ disk.used }} ({{ disk.percent_used }}%)</span>
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
