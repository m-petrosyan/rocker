<script setup>
import { Link } from '@inertiajs/vue3';
import EventIcon from '@/Components/Icons/EventIcon.vue';
import BlogIcon from '@/Components/Icons/BlogIcon.vue';
import GalleryIcon from '@/Components/Icons/GalleryIcon.vue';
import BandIcon from '@/Components/Icons/BandIcon.vue';
</script>
<!--bg-pwaNavbg-->
<template>
    <div
        class="z-50 sticky bottom-0 w-full bg-pwaNavbg">
        <div class="flex items-center gap-x-2 mx-auto w-fit pt-2 text-gray text-sm tracking-widest uppercase">

            <Link
                class="p-2 rounded-full"
                :href="route('events')">
                <EventIcon />
            </Link>
            <Link
                class="p-2 rounded-full"
                :href="route('events')">
                <BlogIcon />
            </Link>
            <Link
                class="hover:bg-orange p-2 rounded-full"
                :href="route('events')">
                <img src="/images/logo.png" alt="logo" class="w-10 logo" />
            </Link>
            <Link
                class="p-2 rounded-full"
                :href="route('home')">
                <GalleryIcon />
            </Link>
            <Link
                class="p-2 rounded-full"
                :href="route('events')">
                <BandIcon />
            </Link>

            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Profile
            </Link>
        </div>
    </div>
</template>
<style class="scss">
a:hover {
    svg {
        fill: theme('colors.orange');
    }

    opacity: 1;
}
</style>
