<script setup>
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';

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
        <h2 v-if="title" class="text-center">{{ title }}</h2>
        <div class="mt-10 grid gap-y-10 md:grid-cols-2 lg:grid-cols-4">
            <NavLink
                v-for="gallery in galleries.data"
                :href="route('galleries.show', gallery.id)"
                :key="gallery.id"
                class="flex flex-col items-center p-4">
                <div class="relative h-64 w-full bg-cover bg-center rounded-lg overflow-hidden">
                    <img v-if="gallery.cover_img.thumb && gallery.cover_img.thumb.trim()"
                         :src="gallery.cover_img.thumb"
                         class="object-cover w-full h-full"
                         alt="Loading"
                         @error="$event.target.src = gallery.cover_img.original" />
                    <img v-else-if="gallery.cover_img.original"
                         :src="gallery.cover_img.original"
                         class="object-cover w-full h-full"
                         alt="Loading" />
                    <div class="absolute right-0 bottom-8 px-1 bg-red">
                        {{ gallery.date }}
                    </div>
                    <NavLink :href="route('profile.show', gallery.user.username)" v-if="gallery.user"
                             class="text-center absolute bottom-0 left-0 w-full p-1 bg-blackTransparent2">
                        by {{ gallery.user.name }}
                    </NavLink>
                    <div v-if="owner || isAdmin"
                         class="absolute bottom-0 w-full h-full flex flex-col justify-between  p-1 bg-blackTransparent2">
                        <div class="flex justify-end">
                            <div class="flex gap-x-2 items-center">
                                <EyesIcon />
                                <p>{{ gallery.views }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <NavLink :href="route('profile.galleries.edit', gallery.id)">
                                <EditIcon />
                            </NavLink>
                            <button @click.prevent="deleteGallery(gallery.id)"
                                    class="text-red-500 hover:text-red-700">
                                <DeleteIcon />
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <h3 class="text-lg font-semibold text-pretty">{{ gallery.title }}</h3>
                </div>
            </NavLink>
            <NavLink
                v-if="add"
                :href="route('profile.galleries.create')"
                class="flex min-h-64 items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2"
            >
                <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
                    <h2 class="text-3xl">+</h2>
                    <h3>Add gallery</h3>
                </div>
            </NavLink>
        </div>
        <div v-if="more"
             class="col-span-full text-center py-4">
            <NavLink :href="route('galleries.index')"
                     class="text-orange font-bold">
                See more
            </NavLink>
        </div>
    </div>
</template>
