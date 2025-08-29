<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import moment from 'moment-timezone';
import GoogleMap from '@/Components/Maps/GoogleMap.vue';
import GoogleCalendar from '@/Components/Socials/GoogleCalendar.vue';
import AppleCalendar from '@/Components/Socials/AppleCalendar.vue';
import MoneyIcon from '@/Components/Icons/MoneyIcon.vue';
import TicketIcon from '@/Components/Icons/TicketIcon.vue';
import UrlIcon from '@/Components/Icons/UrlIcon.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import { removePostalCode } from '@/Helpers/adressFormatHelper.js';
import BandTags from '@/Components/Tags/BandTags.vue';

const props = defineProps({
    event: {
        type: Object
    },
    views: {
        type: Number
    },
    notify_count: {
        type: Number
    },
    url: {
        type: String
    }
});
</script>

<template>
    <GuestLayout
        :meta="{ title: event.title, image: event.poster, description: event.content, keywords: event.title+', '+event.genre+', '+event.location}">
        <div
            class="relative h-96 w-full"
            :style="{ backgroundImage: `url(${event.poster})`}">
            <div class="absolute inset-0 z-0 brightness-50 backdrop-blur-md"></div>
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
                class="absolute top-0 right-0 z-20 flex bg-black bg-opacity-20">
                <SocialShare :title="event.title" :url />
            </div>

            <div class="absolute bottom-0 left-0 z-20 flex gap-x-6 rounded-r-xl bg-black bg-opacity-20 p-4">
                <GoogleCalendar :event />
                <AppleCalendar :event />
            </div>
            <div
                class="flex flex-col absolute bottom-0 right-0 z-20 flex gap-x-6 rounded-r-xl bg-black bg-opacity-20 p-4"
            >
                <div v-if="event.price" class="flex items-center gap-x-2">
                    <MoneyIcon />
                    {{ event.price }}
                </div>
                <a v-if="event.ticket" :href="event.ticket" class="flex items-center gap-x-2" target="_blank">
                    <TicketIcon />
                    Ticket
                </a>
                <a v-if="event.link" :href="event.link" class="flex items-center gap-x-2" target="_blank">
                    <UrlIcon />
                    Link
                </a>
            </div>
        </div>
        <h2 class="mt-6 text-center text-2xl">{{ event.title }}</h2>
        <BandTags class="mx-auto w-fit my-10" :bands="event.bands" />
        <pre class="mt-8 text-pretty text-center">{{ event.content }}</pre>
        <p class="text-center text-orange">{{ removePostalCode(event.location) }}</p>
        <GoogleMap class="mt-5" v-if="event.cordinates" :cordinates="event.cordinates" />
    </GuestLayout>
</template>
