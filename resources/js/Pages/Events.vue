<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import moment from 'moment-timezone';


defineProps({
    events: {
        type: Array,
        default: () => []
    }
});

</script>

<template>
    <GuestLayout :meta="{title: 'Events'}">
        <div class="flex flex-col max-w-screen-sm md:max-w-screen-xl mx-auto">
            <div class="mb-10 text-gray">
                <h1 class="text-xl font-bold text-center mb-5">Events</h1>
            </div>
            <div class="grid  md:grid-cols-2 lg:grid-cols-4 md:grid-rows-6 md:gap-4 gap-y-10">
                <div v-for="event in events.data" :key="event.id" class="mb-2 h-[600px] md:h-[400px]">
                    <div class="relative h-full" :style="{ backgroundImage: `url(${event.poster})`}">
                        <div class="absolute inset-0 backdrop-blur-md z-0 brightness-50"></div>
                        <img :src="event.poster" :alt="event.title"
                             class="absolute w-full h-full object-contain object-center z-10" />
                        <div
                            class="absolute inset-0 bg-orange text-xl w-28 h-28 z-20 flex flex-col justify-center items-center">
                            <p class="text-4xl font-bold">
                                {{ moment(event.start_date, 'DD.MM.YY').format('D').toUpperCase() }}</p>
                            <p>{{ moment(event.start_date, 'DD.MM.YY').format('MMMM').toUpperCase() }}</p>
                            <small>{{ event.start_time }}</small>
                        </div>
                        <div
                            class="absolute z-20 bottom-0 w-full h-48 md:h-52 bg-gradient-to-t from-black to-transparent">
                            <h3 class="text-2xl text-center pt-20">{{ event.title }} </h3>
                            <p class="text-center text-gray-300">{{ event.location }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
