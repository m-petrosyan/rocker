<script setup>
import { Link } from '@inertiajs/vue3';
import menu from '@/Constants/menu.js';
import ProfileIcon from '@/Components/Icons/ProfileIcon.vue';
</script>
<!--bg-pwaNavbg-->
<template>
    <div
        class="z-50 sticky bottom-0 w-full bg-pwaNavbg">
        <div class="flex items-center gap-x-2 mx-auto w-fit pt-2 text-gray text-sm tracking-widest uppercase">
            <Link
                v-for="item in menu"
                :key="item.name"
                class="p-2 rounded-full"
                :href="item.url">
                <img
                    v-if="item.img"
                    :src="item.img"
                    alt="logo"
                    class="logo w-10"
                />
                <component v-if="item.icon" :is="item.icon" />
            </Link>

            <Link
                v-if="$page.props.auth.user"
                :href="route('profile.index')"
                class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                <ProfileIcon />
            </Link>
        </div>
    </div>
</template>
<style scoped class="scss">
a:hover {
    svg {
        fill: theme('colors.orange');
    }

    opacity: 1;
}
</style>
