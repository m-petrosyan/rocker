<script setup>
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';

defineProps({
  blogs: {
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

const deleteBlog = (id) => {
  if (confirm('Are you sure you want to delete this blog?')) {
    router.delete(route('profile.blogs.destroy', id), {
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
        v-for="blog in blogs"
        :href="route('blogs.show', blog.slug)"
        :key="blog.id"
        class="flex flex-col items-center p-4">
        <div class="relative h-64 w-full rounded-lg bg-black">
          <div class="h-full w-full bg-black rounded-lg rounded-xl overflow-hidden">
            <img v-if="blog.cover.thumb && blog.cover.thumb.trim()"
                 :src="blog.cover.thumb"
                 class="object-cover w-full h-full"
                 alt="Loading"
                 @error="$event.target.src = blog.cover.original" />
            <img v-else-if="blog.cover.original"
                 :src="blog.cover.original"
                 class="object-cover w-full h-full"
                 alt="Loading" />
          </div>
          <div v-if="owner || isAdmin"
               class="absolute bottom-0 w-full h-full flex flex-col justify-end  p-1 bg-blackTransparent2">
            <div class="flex justify-between">
              <NavLink v-tooltip="'Edit'" :href="route('profile.blogs.edit', blog.id)">
                <EditIcon />
              </NavLink>
              <button v-tooltip="'Delete'" @click.prevent="deleteBlog(blog.id)"
                      class="text-red-500 hover:text-red-700">
                <DeleteIcon />
              </button>
            </div>
          </div>
        </div>
        <div class="p-2">
          <p class="text-lg font-semibold text-pretty">{{ blog.title['en'] ?? blog.title['am'] ?? blog.title['ru']
            }}</p>
        </div>
      </NavLink>
      <NavLink
        v-if="add"
        :href="route('profile.blogs.create')"
        class="flex min-h-64 items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2"
      >
        <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
          <h2 class="text-3xl">+</h2>
          <h3>Add blog</h3>
        </div>
      </NavLink>
    </div>
    <div v-if="more"
         class="col-span-full text-center py-4">
      <NavLink :href="route('blogs.index')"
               label="Blogs list"
               class="text-orange font-bold">
        See more blogs
      </NavLink>
    </div>
  </div>
</template>
