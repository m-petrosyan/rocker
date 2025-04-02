<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import moment from 'moment-timezone';
import Map from '@/Components/Map.vue';

defineProps({
    event: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <GuestLayout :meta="{title: 'Events'}">
        <!--        {{ event }}-->

        <div>
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
            </div>
        </div>
        <pre class="text-pretty">{{ event.content }}</pre>
        {{ event.cordinates.longitude }}
        {{ event.cordinates.latitude }}
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
