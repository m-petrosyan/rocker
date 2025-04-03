<script setup>
import { Link } from '@inertiajs/vue3';

const menu = [
    { name: 'Events', url: route('events.index') },
    { name: 'Blog', url: route('events.index') },
    { name: '', href: route('events.index'), img: '/images/logo.png' },
    { name: 'Gallery', url: route('gallery') },
    { name: 'Bands', url: route('events.index') }
];
</script>

<template>
    <nav class="flex items-center gap-x-2 mx-auto pt-5 text-gray text-sm tracking-widest uppercase">
        <div class="flex items-center gap-x-2 mx-auto w-fit">
            <Link
                v-for="item in menu" :key="item.name"
                class="hover:opacity-70 transition"
                :href="item.url">
                <img v-if="item.img" :src="item.img" alt="logo" class="w-10 logo" />
                <span v-if="item.name">{{ item.name }}</span>
            </Link>
        </div>
        <Link
            v-if="$page.props.auth.user"
            :href="route('profile.index')"
            class="absolute right-0 rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            Profile
        </Link>
    </nav>
</template>
<style>

</style>
