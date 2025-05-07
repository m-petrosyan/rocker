<script setup>
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';

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
</script>

<template>
    <div class="mt-10">
        <h2 v-if="title" class="text-center">{{ title }}</h2>
        <div class="mt-10 grid gap-y-10 md:grid-cols-2 lg:grid-cols-4">
            <NavLink
                v-for="band in bands.data"
                :href="route('bands.show', band.slug)"
                :key="band.id"
                class="flex flex-col items-center p-4">
                <div class="relative h-64 w-full rounded-lg overflow-hidden bg-black">
                    <img v-if="band.logo.thumb && band.logo.thumb.trim()"
                         :src="band.logo.thumb"
                         class="object-contain w-full h-full"
                         alt="Loading"
                         @error="$event.target.src = band.logo.original" />
                    <img v-else-if="band.logo.original"
                         :src="band.logo.original"
                         class="object-contain w-full h-full"
                         alt="Loading" />
                    <div v-if="owner || isAdmin"
                         class="absolute bottom-0 w-full h-full flex flex-col justify-end  p-1 bg-blackTransparent2">
                        <div class="flex justify-between">
                            <NavLink :href="route('profile.bands.edit', band.id)">
                                <EditIcon />
                            </NavLink>
                            <button @click.prevent="deleteBand(band.id)"
                                    class="text-red-500 hover:text-red-700">
                                <DeleteIcon />
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <h3 class="text-lg font-semibold text-pretty">{{ band.name }}</h3>
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
            <NavLink :href="route('bands.index')"
                     class="text-orange font-bold">
                See more
            </NavLink>
        </div>
    </div>
</template>
