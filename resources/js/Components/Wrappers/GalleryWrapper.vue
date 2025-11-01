<script setup>
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import { formatDateTime } from '@/Helpers/dateFormatHelper.js';

defineProps({
  galleries: {
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
  },
  profile: {
    type: Boolean,
    default: false
  }
});

const deleteGallery = (id) => {
  if (confirm('Are you sure you want to delete this gallery?')) {
    router.delete(route('profile.galleries.destroy', id), {
      preserveState: false,
      preserveScroll: true
    });
  }
};
</script>

<template>
  <div class="mt-10">
    <h3 v-if="title" class="text-center">{{ title }}</h3>
    <div class="mt-10 grid gap-y-10 md:grid-cols-2 lg:grid-cols-4">
      <NavLink
        v-for="gallery in galleries"
        :href="route('galleries.show', gallery.id)"
        :key="gallery.id"
        class="flex flex-col items-center p-4">
        <div class="relative h-64 w-full bg-black">
          <div class="h-full w-full bg-black rounded-lg rounded-xl overflow-hidden">
            <img :src="gallery.cover_img.thumb"
                 class="object-cover w-full h-full"
                 loading="lazy"
                 :alt="gallery.title"
                 @error="$event.target.src = gallery.cover_img.original" />
          </div>
          <div v-if="isAdmin" class="absolute left-0 top-0 px-1 bg-red">
            {{ gallery.total_mb }} MB
          </div>
          <div class="absolute right-0 bottom-8 px-1 bg-orange">
            {{ formatDateTime(gallery.date, 'DD.MM.YY') }}
          </div>
          <a v-if="gallery.user"
             :href="route('profile.show', gallery.user.username)"
             class="text-center absolute bottom-0 left-0 w-full p-1 bg-blackTransparent2"
             @click.stop>
            by {{ gallery.user.name }}
          </a>
          <div v-if="(owner || isAdmin) && profile"
               class="absolute bottom-0 w-full h-full flex flex-col justify-between  p-1 bg-blackTransparent2">
            <div class="flex justify-end gap-y-2">
              <div tooltip="Views"
                   class="flex gap-x-2 items-center">
                <EyesIcon />
                <b>{{ gallery.views_count }}</b>
              </div>
            </div>

            <div class="flex justify-between">
              <NavLink tooltip="Edit"
                       :href="route('profile.galleries.edit', gallery.id)">
                <EditIcon />
              </NavLink>
              <button tooltip="Delete" @click.prevent="deleteGallery(gallery.id)"
                      class="text-red-500 hover:text-red-700">
                <DeleteIcon />
              </button>
            </div>
          </div>
        </div>
        <div class="p-2">
          <!--          <p v-if="isSSR" class="text-lg font-semibold">{{ gallery.title }} by {{ gallery.user?.name }}</p>-->
          <p class="text-lg font-semibold">{{ gallery.title }}</p>
        </div>
      </NavLink>
      <NavLink
        v-if="add"
        :href="route('profile.galleries.create')"
        class="flex min-h-64 items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2"
      >
        <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
          <h2 class="text-3xl">+</h2>
          <h3 class="text-nowrap">Add gallery</h3>
        </div>
      </NavLink>
    </div>
    <div v-if="more"
         class="col-span-full text-center py-4">
      <NavLink :href="route('galleries.index')"
               label="Galleries list"
               class="text-orange font-bold">
        Browse more photo galleries
      </NavLink>
    </div>
  </div>
</template>
