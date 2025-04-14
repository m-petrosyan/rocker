<script setup>
import NavLink from '@/Components/NavLink.vue';

defineProps({
    galleries: {
        type: Array,
        required: true
    },
    title: {
        type: String,
        default: ''
    },
    user: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div class="mt-10">
        <h2 v-if="title" class="text-center">{{ title }}</h2>
        <div class="flex">
            <div class="mt-10 grid gap-y-10 md:grid-cols-2 lg:grid-cols-4">
                <NavLink :href="route('profile.index')"
                         v-for="gallery in galleries"
                         :key="gallery.id"
                         class="flex flex-col items-center p-4"
                >
                    <div class="relative h-64 w-full bg-cover bg-center rounded-lg"
                         :style="{ backgroundImage: `url(${gallery.cover.thumb})` }">
                        <NavLink :href="route('profile.show', gallery.user.username)" v-if="user"
                                 class="absolute bottom-0 left-0 w-full p-1 bg-blackTransparent2">
                            by {{ gallery.user.name }}
                        </NavLink>
                    </div>
                    <div class="p-2">
                        <h3 class="text-lg font-semibold text-pretty">{{ gallery.title }}</h3>
                    </div>
                </NavLink>
            </div>
        </div>
    </div>
</template>
