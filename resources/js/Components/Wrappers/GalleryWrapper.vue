<script setup>
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import { isSSR } from '@/Helpers/ssrHelper.js';

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

    <!-- Гибкая ровная сетка -->
    <div
      class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4 auto-rows-[1fr]"
    >
      <NavLink
        v-for="gallery in galleries"
        :href="route('galleries.show', gallery.id)"
        :key="gallery.id"
        class="flex flex-col bg-transparent rounded-lg overflow-hidden h-full"
      >
        <div class="relative w-full aspect-[4/3] bg-black">
          <img
            v-if="gallery.cover_img.thumb && gallery.cover_img.thumb.trim()"
            :src="gallery.cover_img.thumb"
            class="object-cover w-full h-full"
            loading="lazy"
            :alt="gallery.title"
            @error="$event.target.src = gallery.cover_img.original"
          />
          <img
            v-else-if="gallery.cover_img.original"
            :src="gallery.cover_img.original"
            class="object-cover w-full h-full"
            alt="Loading"
          />

          <div v-if="isAdmin" class="absolute left-0 top-0 px-1 bg-red">
            {{ gallery.total_mb }} MB
          </div>
          <div class="absolute right-0 bottom-8 px-1 bg-orange">
            {{ gallery.date }}
          </div>
          <a
            v-if="gallery.user"
            :href="route('profile.show', gallery.user.username)"
            class="absolute bottom-0 left-0 w-full p-1 bg-blackTransparent2 text-center"
            @click.stop
          >
            by {{ gallery.user.name }}
          </a>

          <div
            v-if="(owner || isAdmin) && profile"
            class="absolute inset-0 flex flex-col justify-between p-1 bg-blackTransparent2"
          >
            <div class="flex justify-end gap-y-2">
              <div class="flex gap-x-2 items-center">
                <EyesIcon />
                <b tooltip="Unique views">{{ gallery.views }}</b> /
                <p tooltip="All views">{{ gallery.allViews }}</p>
              </div>
            </div>

            <div class="flex justify-between">
              <NavLink tooltip="Edit" :href="route('profile.galleries.edit', gallery.id)">
                <EditIcon />
              </NavLink>
              <button
                tooltip="Delete"
                @click.prevent="deleteGallery(gallery.id)"
                class="text-red-500 hover:text-red-700"
              >
                <DeleteIcon />
              </button>
            </div>
          </div>
        </div>

        <!-- Нижняя часть с текстом -->
        <div class="p-2 flex-1 flex flex-col justify-end">
          <p class="text-lg font-semibold truncate text-center">
            {{ gallery.title }}
            <span v-if="isSSR && gallery.user"> by {{ gallery.user.name }}</span>
          </p>
        </div>
      </NavLink>

      <!-- Карточка добавления -->
      <NavLink
        v-if="add"
        :href="route('profile.galleries.create')"
        class="flex flex-col items-center justify-center bg-transparent rounded-lg h-full aspect-[4/3]"
      >
        <div class="mx-auto flex flex-col items-center gap-y-4">
          <h2 class="text-3xl">+</h2>
          <h3>Add gallery</h3>
        </div>
      </NavLink>
    </div>

    <div v-if="more" class="col-span-full text-center py-4">
      <NavLink :href="route('galleries.index')" class="text-orange font-bold">
        Browse more photo galleries
      </NavLink>
    </div>
  </div>
</template>

