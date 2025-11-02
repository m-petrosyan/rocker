<script setup>
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import { ref } from 'vue';

defineProps({
  bands: {
    type: Object,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  user: {
    type: Boolean,
    default: false
  },
  owner: {
    type: Boolean,
    default: false
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  add: {
    type: Boolean,
    default: false
  },
  more: {
    type: Boolean,
    default: false
  }
});

const deleteBand = (id) => {
  if (confirm('Are you sure you want to delete this band?')) {
    router.delete(route('profile.bands.destroy', id), {
      preserveState: false,
      preserveScroll: true
    });
  }
};


const wideFlags = ref({});

const onImgLoad = (e, id) => {
  const { naturalWidth, naturalHeight } = e.target;
  wideFlags.value[id] = naturalWidth > naturalHeight;
};
</script>

<template>
  <div class="mt-10">
    <h3 v-if="title" class="text-center">{{ title }}</h3>
    <div class="mt-10 grid gap-10 md:grid-cols-2 lg:grid-cols-4">
      <NavLink
        v-for="band in bands"
        :href="route('bands.show', band.slug)"
        :key="band.id"
        class="flex flex-col items-center">
        <div class="relative aspect-square w-full ">
          <div class="lg:h-[300px] h-full w-full rounded-lg rounded-xl overflow-hidden">
            <div class="bg-black w-full h-full">
              <img
                :src="band.logo?.svg ?? band.logo.thumb"
                @load="e => onImgLoad(e, band.id)"
                class="w-full h-full object-center "
                :class="wideFlags[band.id] ? 'object-contain' : 'object-cover'"
                :alt="band.name"
                @error="$event.target.src = band.logo.original"
              />
            </div>

          </div>

          <div v-if="owner || isAdmin"
               class="absolute inset-0 flex flex-col justify-between p-1 bg-blackTransparent2">
            <div class="flex justify-end gap-y-2">
              <div tooltip="Views" class="flex items-center">
                <EyesIcon />
                {{ band.views_count }}
              </div>
            </div>
            <div class="flex justify-between">
              <NavLink tooltip="Edit" :href="route('profile.bands.edit', band.id)">
                <EditIcon />
              </NavLink>
              <button tooltip="Delete" @click.prevent="deleteBand(band.id)"
                      class="text-red-500 hover:text-red-700">
                <DeleteIcon />
              </button>
            </div>
          </div>
        </div>


        <div class="p-2">
          <p class="text-lg font-semibold text-pretty">
            {{ band.name.length > 40 ? band.name.slice(0, 40) + '...' : band.name }}</p>
        </div>
      </NavLink>
      <NavLink
        v-if="add"
        :href="route('profile.bands.create')"
        class="flex min-h-64 items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2"
      >
        <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
          <h2 class="text-3xl">+</h2>
          <h3>Add band</h3>
        </div>
      </NavLink>
    </div>
    <div v-if="more"
         class="col-span-full text-center py-4">
      <NavLink :href="route('bands.index')" label="Bands list"
               class="text-orange font-bold">
        Discover more bands
      </NavLink>
    </div>
  </div>
</template>
