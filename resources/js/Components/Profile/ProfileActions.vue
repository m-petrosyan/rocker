<script setup>
import NavLink from '@/Components/NavLink.vue';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import BlogIcon from '@/Components/Icons/BlogIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';

const menu = [
    {
        name: 'Add event',
        route: 'events.index',
        icon: EventIcon
    },
    {
        name: 'Add blog',
        route: 'settings.edit',
        icon: BlogIcon
    },
    {
        name: 'Add gallery',
        route: 'settings.edit',
        icon: GalleryIcon
    },
    {
        name: 'Add band',
        route: 'settings.edit',
        icon: BandIcon
    }
];
</script>

<template>
    <div class="grid grid-cols-4 mt-6">
        <NavLink v-for="item in menu" :key="item.name" href="{{ route(item.route) }}"
                 class="flex items-center gap-2 p-4">
            <div class="flex flex-col gap-y-4 items-center bg-black hover:bg-orange w-32 p-4 rounded-lg mx-auto ">
                <component :is="item.icon" />
                <h3>{{ item.name }}</h3>
            </div>
        </NavLink>
    </div>
</template>
