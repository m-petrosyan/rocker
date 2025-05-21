<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
    users: {
        type: Array
    }
});
</script>

<template>
    <ProfileLayout>
        <div
            class="grid md:grid-cols-2 md:gap-4 lg:grid-cols-4 auto-rows-[600px] md:auto-rows-[400px] gap-4">
            <NavLink :href="route('profile.show', user.username)" v-for="user in users.data"
                     class="text-center flex flex-col items-center gap-4" href="">
                <div :style="{ backgroundImage: `url(${user.image?.thumb ?? '/images/user.jpg'})` }"
                     class="h-48 w-48 bg-no-repeat bg-contain  bg-cover">
                </div>
                <div>
                    <div>
                        <p>{{ user.name }}</p>
                        <p>last acivity</p>
                    </div>
                    <div class="mt-4 flex gap-4">
                        <div class="flex flex-col">
                            <b>{{ user.bands_count }}</b>
                            <p>Bands</p>
                        </div>
                        <div class="flex flex-col">
                            <b>{{ user.events_count }}</b>
                            <p>Events</p>
                        </div>
                        <div class="flex flex-col">
                            <b>{{ user.galleries_count }}</b>
                            <p>Gallery</p>
                        </div>
                        <div class="flex flex-col">
                            <b>{{ user.blogs_count }}</b>
                            <p>Blog</p>
                        </div>
                    </div>
                </div>
            </NavLink>
        </div>
    </ProfileLayout>
</template>
