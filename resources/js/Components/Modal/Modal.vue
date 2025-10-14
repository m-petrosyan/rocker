<script setup>

defineProps({
  show: {
    type: Boolean,
    required: true
  }
});
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-graydark rounded-lg p-6 w-96 shadow-lg">
      <slot />
    </div>
  </div>
</template>
