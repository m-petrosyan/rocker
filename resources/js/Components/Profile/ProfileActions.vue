<script setup>
import NavLink from '@/Components/NavLink.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';

const menu = [
    {
        name: 'Add event',
        route: route('profile.events.create'),
        icon: EventIcon
    },
    // {
    //     name: 'Add blog',
    //     route: route('profile.edit'),
    //     icon: BlogIcon
    // },
    {
        name: 'Add gallery',
        route: route('profile.gallery.create'),
        icon: GalleryIcon
    },
    {
        name: 'Add band',
        route: route('profile.edit'),
        icon: BandIcon
    }
];
</script>

<template>
    <div class="mt-6 grid grid-cols-4">
        <NavLink
            v-for="item in menu"
            :key="item.name"
            :href="item.route"
            class="flex items-center gap-2 p-4"
        >
            <div
                class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg bg-black p-4 transition-all duration-300 hover:bg-orange"
            >
                <component :is="item.icon" />
                <h3>{{ item.name }}</h3>
            </div>
        </NavLink>
    </div>
</template>
