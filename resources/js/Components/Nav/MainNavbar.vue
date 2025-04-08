<script setup>
import { Link } from '@inertiajs/vue3';

const menu = [
    { name: 'Events', url: route('events.index') },
    { name: 'Blog', url: route('events.index') },
    { name: '', href: route('events.index'), img: '/images/logo.png' },
    { name: 'Gallery', url: route('gallery') },
    { name: 'Bands', url: route('events.index') },
];
</script>

<template>
    <nav
        class="mx-auto flex items-center gap-x-2 pt-5 text-sm uppercase tracking-widest text-gray"
    >
        <div class="mx-auto flex w-fit items-center gap-x-2">
            <Link
                v-for="item in menu"
                :key="item.name"
                class="transition hover:opacity-70"
                :href="item.url"
            >
                <img
                    v-if="item.img"
                    :src="item.img"
                    alt="logo"
                    class="logo w-10"
                />
                <span v-if="item.name">{{ item.name }}</span>
            </Link>
        </div>
        <div class="absolute right-0">
            <template v-if="$page.props.auth.user">
                <Link
                    :href="
                        route('profile.index', $page.props.auth.user.username)
                    "
                    class="px-3 py-2 text-white"
                >
                    Profile
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="px-3 py-2 text-white"
                    >Log Out
                </Link>
            </template>
            <template v-else>
                <Link :href="route('login')" class="px-3 py-2 text-white"
                    >Log In
                </Link>
                <Link :href="route('register')" class="px-3 py-2 text-white"
                    >Register
                </Link>
            </template>
        </div>
    </nav>
</template>
<style></style>
