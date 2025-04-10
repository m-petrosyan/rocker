<script setup>
import { Link } from '@inertiajs/vue3';

const menu = [
    { name: 'Events', url: route('events.index') },
    { name: 'Gallery', url: route('gallery') },
    { name: '', href: route('events.index'), img: '/images/logo.png' },
    // { name: 'Blog', url: route('events.index') },
    { name: 'Bands', url: route('events.index') }
];
</script>

<template>
    <nav
        class="mx-auto flex items-center gap-x-2 pt-5 text-sm uppercase tracking-widest text-gray">
        <div class="mx-auto flex w-fit items-center gap-x-2">
            <Link
                v-for="item in menu"
                :key="item.name"
                class="transition hover:opacity-70"
                :href="item.url">
                <img
                    v-if="item.img"
                    :src="item.img"
                    alt="logo"
                    class="logo w-10"
                />
                <span v-if="item.name">{{ item.name }}</span>
            </Link>
        </div>
        <div class="absolute right-2 flex gap-x-4 uppercase">
            <template v-if="$page.props.auth.user">
                <Link :href="route('profile.show', {'username': $page.props.auth.user.username})">
                    Profile
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="uppercase">
                    Log Out
                </Link>
            </template>
            <template v-else>
                <Link :href="route('login')">
                    Log In
                </Link>
                <Link :href="route('register')">
                    Register
                </Link>
            </template>
        </div>
    </nav>
</template>
<style></style>
