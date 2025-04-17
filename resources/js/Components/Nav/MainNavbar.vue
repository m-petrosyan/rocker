<script setup>
import { Link } from '@inertiajs/vue3';
import menu from '@/Constants/menu.js';
import { onBeforeUnmount, ref, watch } from 'vue';

const showBurger = ref(false);

const bugerMenu = [...menu].sort((a, b) => {
    if (a.img && !b.img) return -1;
    if (!a.img && b.img) return 1;
    return 0;
});

watch(showBurger, (val) => {
    if (val) {
        document.body.classList.add('overflow-hidden');
    } else {
        document.body.classList.remove('overflow-hidden');
    }
});

onBeforeUnmount(() => {
    if (document.body.classList.contains('overflow-hidden')) {
        document.body.classList.remove('overflow-hidden');
    }
});
</script>

<template>
    <nav
        class="hidden md:block ml-8 mx-auto flex items-center gap-x-2 pt-5 text-sm uppercase tracking-widest text-gray">
        <div class="mx-auto flex w-fit items-center gap-x-2">
            <Link
                v-for="item in menu"
                :key="item.name"
                class="transition hover:opacity-70"
                :href="route(item.url)">
                <img
                    v-if="item.img"
                    :src="item.img"
                    alt="logo"
                    class="logo w-10"
                />
                <span v-if="!item.img && item.name">{{ item.name }}</span>
            </Link>
        </div>
        <div class="absolute top-0 right-2 flex gap-x-4 p-5 uppercase">
            <template v-if="$page.props.auth.user">
                <Link :href="route('profile.show', {'username': $page.props.auth.user.username})">
                    Profile
                </Link>
                <!--                <Link-->
                <!--                    :href="route('logout')"-->
                <!--                    method="post"-->
                <!--                    as="button"-->
                <!--                    class="uppercase">-->
                <!--                    Log Out-->
                <!--                </Link>-->
            </template>
            <template v-else>
                <Link :href="route('login')">
                    Log In
                </Link>
            </template>
        </div>
    </nav>
    <Teleport to="body">
        <nav class="absolute md:hidden w-full top-0 left-0 "
             :class="{'bg-blackTransparent z-50 h-lvh' : showBurger}">
            <div class=" flex justify-end">
                <button @click="showBurger = !showBurger" data-collapse-toggle="navbar-hamburger" type="button"
                        class="inline-flex items-center justify-center p-2 w-14 h-14 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-hamburger" aria-expanded="false">
                    <svg class="w-8 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div v-if="showBurger" class="h-full mt-[-40px]">
                <ul class="w-full h-full flex flex-col justify-center items-center gap-y-4 text-gray text-sm uppercase tracking-widest">
                    <li v-for="item in bugerMenu" :key="item.name">
                        <Link
                            class="transition hover:opacity-70"
                            :href="route(item.url)">
                            <h2 v-if="item.name">{{ item.name }}</h2>
                        </Link>
                    </li>
                    <li v-if="$page.props.auth.user">
                        <Link
                            :href="route('profile.show', {'username': $page.props.auth.user.username})">
                            <h2>Profile</h2>
                        </Link>
                    </li>
                    <li v-if="$page.props.auth.user">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="uppercase">
                            <h2> Log Out</h2>
                        </Link>
                    </li>
                    <li v-else>
                        <Link :href="route('login')">
                            <h2> Log In</h2>
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>
    </Teleport>
</template>
<style></style>
