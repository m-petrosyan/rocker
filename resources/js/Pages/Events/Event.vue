<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import moment from 'moment-timezone';
import GoogleMap from '@/Components/Maps/GoogleMap.vue';
import GoogleCalendar from '@/Components/Socials/GoogleCalendar.vue';
import AppleCalendar from '@/Components/Socials/AppleCalendar.vue';

const props = defineProps({
    event: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <GuestLayout :meta="{ title: 'Events' }">
        <!--        {{ event }}-->

        <div
            class="relative h-96 w-full"
            :style="{ backgroundImage: `url(${event.poster})` }"
        >
            <div
                class="absolute inset-0 z-0 brightness-50 backdrop-blur-md"
            ></div>
            <img
                :src="event.poster"
                :alt="event.title"
                class="absolute z-10 h-96 w-full object-contain object-center"
            />
            <div
                class="absolute inset-0 z-20 flex h-28 w-28 flex-col items-center justify-center bg-orange text-xl"
            >
                <p class="text-4xl font-bold">
                    {{
                        moment(event.start_date, 'DD.MM.YY')
                            .format('D')
                            .toUpperCase()
                    }}
                </p>
                <p>
                    {{
                        moment(event.start_date, 'DD.MM.YY')
                            .format('MMMM')
                            .toUpperCase()
                    }}
                </p>
                <small>{{ event.start_time }}</small>
            </div>
            <div
                class="h-54 absolute bottom-0 left-0 z-20 flex gap-x-6 rounded-r-xl bg-black bg-opacity-20 p-4"
            >
                <GoogleCalendar :event />
                <AppleCalendar :event />
            </div>
        </div>

        <h3 class="mt-2 text-center text-2xl">{{ event.title }}</h3>
        <pre class="mt-8 text-pretty text-center">{{ event.content }}</pre>
        <p class="text-center text-orange">{{ event.location }}</p>
        <GoogleMap :cordinates="event.cordinates" />

        <!--        <div-->
        <!--            class="absolute inset-0 bg-orange text-xl w-28 h-28 z-20 flex flex-col justify-center items-center">-->
        <!--            <p class="text-4xl font-bold">-->
        <!--                {{ moment(event.start_date, 'DD.MM.YY').format('D').toUpperCase() }}</p>-->
        <!--            <p>{{ moment(event.start_date, 'DD.MM.YY').format('MMMM').toUpperCase() }}</p>-->
        <!--            <small>{{ event.start_time }}</small>-->
        <!--        </div>-->
    </GuestLayout>
</template>
