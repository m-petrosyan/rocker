<script setup>
import NavLink from '@/Components/NavLink.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import BlogIcon from '@/Components/Icons/BlogIcon.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
  full: {
    type: Boolean,
    default: true
  }
});

const menu = ref([
  {
    name: 'Add event',
    route: 'profile.events.create',
    icon: EventIcon
  }
]);

onMounted(() => {
  if (props.full) {
    menu.value.push(
      {
        name: 'Add band',
        route: 'profile.bands.create',
        icon: BandIcon
      },
      {
        name: 'Add gallery',
        route: 'profile.galleries.create',
        icon: GalleryIcon
      },
      {
        name: 'Add blog',
        route: 'profile.blogs.create',
        icon: BlogIcon
      }
    );
  }
});
</script>

<template>
  <div class="mt-6 grid grid-cols-2 md:grid-cols-3 w-full md:w-4/6">
    <NavLink
      v-for="item in menu"
      :key="item.name"
      :href="route(item.route)"
      :disabled="item.disabled"
      class="flex items-center gap-2 p-4"
      :class="{ 'opacity-50 pointer-events-none': item.disabled }">
      <div
        class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg bg-black p-4 transition-all duration-300 hover:bg-orange">
        <component :is="item.icon" />
        <h4 class="text-nowrap">{{ item.name }}</h4>
      </div>
    </NavLink>
  </div>
</template>
