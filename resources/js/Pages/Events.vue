<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    events: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <GuestLayout :meta="{title: 'Events'}">
        <div class="flex flex-col max-w-screen-sm mx-auto">
            <h1 class="text-2xl font-bold">Events</h1>
            <div class="flex flex-col gap-y-10">
                <div v-for="event in events.data" :key="event.id" class="mb-2 h-[600px]">
                    <div class="relative h-full" :style="{ backgroundImage: `url(${event.poster})`}">
                        <div class="absolute inset-0 backdrop-blur-md z-0 brightness-50"></div>
                        <img :src="event.poster" :alt="event.title"
                             class="absolute w-full h-full object-contain object-center z-10" />
                        <div class="absolute z-20 bottom-0 w-full h-48 bg-gradient-to-t from-black to-transparent">
                            <h3 class="text-2xl text-center pt-20">{{ event.title }} </h3>
                            <p class="text-center text-gray-300">{{ event.location }}</p>
                            <p class="text-center"><b>{{ event.start_date }}</b> <span>{{ event.start_time }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
