<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import moment from 'moment-timezone';
import Map from '@/Components/Map.vue';
import GoogleCalendar from '@/Components/Socials/GoogleCalendar.vue';
import AppleCalendar from '@/Components/Socials/AppleCalendar.vue';

const props = defineProps({
    event: {
        type: Array,
        default: () => []
    }
});

</script>

<template>
    <GuestLayout :meta="{title: 'Events'}">
        <!--        {{ event }}-->


        <div class="relative h-96 w-full"
             :style="{ backgroundImage: `url(${event.poster})`}">
            <div class="absolute inset-0 backdrop-blur-md z-0 brightness-50"></div>
            <img :src="event.poster" :alt="event.title"
                 class="absolute w-full h-96 object-contain object-center z-10" />
            <div
                class="absolute inset-0 bg-orange text-xl w-28 h-28 z-20 flex flex-col justify-center items-center">
                <p class="text-4xl font-bold">
                    {{ moment(event.start_date, 'DD.MM.YY').format('D').toUpperCase() }}</p>
                <p>{{ moment(event.start_date, 'DD.MM.YY').format('MMMM').toUpperCase() }}</p>
                <small>{{ event.start_time }}</small>
            </div>
            <div
                class="bg-opacity-20 bg-black p-4  rounded-r-xl absolute bottom-0 left-0 h-54  z-20 flex gap-x-6">
                <GoogleCalendar :event />
                <AppleCalendar :event />
            </div>
        </div>

        <h3 class="text-2xl text-center mt-2">{{ event.title }} </h3>
        <pre class="text-pretty text-center mt-8">{{ event.content }}</pre>
        <p class="text-center text-orange">{{ event.location }}</p>
        <div class="flex gap-x-6 justify-center my-4">
            <GoogleCalendar :event />
            <AppleCalendar :event />
        </div>
        <Map :cordinates="event.cordinates" />


        <!--        <div-->
        <!--            class="absolute inset-0 bg-orange text-xl w-28 h-28 z-20 flex flex-col justify-center items-center">-->
        <!--            <p class="text-4xl font-bold">-->
        <!--                {{ moment(event.start_date, 'DD.MM.YY').format('D').toUpperCase() }}</p>-->
        <!--            <p>{{ moment(event.start_date, 'DD.MM.YY').format('MMMM').toUpperCase() }}</p>-->
        <!--            <small>{{ event.start_time }}</small>-->
        <!--        </div>-->
    </GuestLayout>
</template>
