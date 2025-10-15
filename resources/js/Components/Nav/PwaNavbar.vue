<script setup>
import { Link } from '@inertiajs/vue3';
import menu from '@/Constants/menu.js';
import ProfileIcon from '@/Components/Icons/ProfileIcon.vue';
import { ref } from 'vue';
import { isWebApp } from '@/Helpers/setAppUser.js';

const isPWA = ref(false);
const webApp = isWebApp();

const pwamenu = webApp
    ? menu.filter(item => item.name !== 'Bot')
    : menu;

</script>

<template>
    <div
        class="z-50 sticky bottom-0 w-full bg-pwaNavbg">
        <div class="flex items-center gap-x-2 mx-auto w-fit pt-2 text-gray text-sm tracking-widest uppercase">
            <component
                v-for="item in pwamenu"
                :key="item.name"
                :is="item.external ? 'a' : Link"
                :href="item.external ? item.url : route(item.url)"
                :target="item.external ? '_blank' : null"
                :rel="item.external ? 'noopener noreferrer' : null"
                :method="!item.external ? 'get' : undefined"
                :preserve-scroll="!item.external"
                class="p-2 rounded-full"
            >
                <img
                    v-if="item.img"
                    :src="item.img"
                    alt="logo"
                    class="logo w-10"
                />
                <component v-if="item.icon" :is="item.icon" />
            </component>
            <Link
                :href="$page.props.auth.user ? route('profile.index') : route('login')"
                class="p-2 top-2 right-2"
                :class="{'absolute' : !webApp}"
            >
                <ProfileIcon />
            </Link>
        </div>
    </div>
</template>
<!--<style scoped class="scss">-->
<!--a:hover {-->
<!--    svg, svg > g {-->
<!--        fill: theme('colors.orange');-->
<!--    }-->

<!--    opacity: 1;-->
<!--}-->
<!--</style>-->
