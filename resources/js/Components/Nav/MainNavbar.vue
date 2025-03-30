<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex gap-x-2 mx-auto w-fit pt-5 text-gray text-sm tracking-widest uppercase">
        <p class=" ">Home</p>
        <Link
            :href="route('home')">
            Menu
        </Link>

        <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            Profile
        </Link>
    </div>
</template>
