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


const type = props.event && props.event.type === 2 ? 'concert' : 'event';
</script>

<template>
    <GuestLayout
        :meta="{ title: event.title+' Armenian Rock & Metal Concerts', image: event.poster, description: event.content, keywords: event.title+', '+event.genre+', '+event.location}">
        <div
            class="relative h-96 w-full"
            :style="{ backgroundImage: `url(${event.poster.large})`}">
            <div class="absolute inset-0 z-0 brightness-50 backdrop-blur-md"></div>
            <img
                :src="event.poster.large"
                :alt="event.title + ' '+ removePostalCode(event.location) "
                class="absolute z-10 h-96 w-full object-contain object-center"
            />
            <div
                class="absolute inset-0 z-20 flex h-28 w-28 flex-col items-center justify-center bg-orange text-xl"
            >
                <p class="text-4xl font-bold">
                    {{
                        moment(event.start_date_short, 'DD.MM.YY')
                            .format('D')
                            .toUpperCase()
                    }}
                </p>
                <p>
                    {{
                        moment(event.start_date_short, 'DD.MM.YY')
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
                    <p> {{ event.price }}</p>
                </div>
                <a v-if="event.ticket" :href="event.ticket" class="flex items-center gap-x-2" target="_blank"
                   aria-label="Buy Ticket">
                    <TicketIcon />
                    <span>Ticket</span>
                </a>
                <a v-if="event.link" :href="event.link" class="flex items-center gap-x-2" target="_blank"
                   aria-label="Event Link">
                    <UrlIcon />
                    <span>Link</span>
                </a>
            </div>
        </div>
        <h1>
            {{ event.title }} – {{ type }}, {{ moment(event.start_date, 'DD MMMM YYYY').format('DD MMMM YYYY') }},
            {{ removePostalCode(event.location) }}
        </h1>

        <h2 class="mt-6 text-center text-2xl">{{ event.title }}</h2>
        <div class="text-center text-red">
            <p>genre: {{ event.genre }}</p>
            <p>type: {{ type }}</p>
        </div>
        <!--        <BandTags class="mx-auto w-fit my-10" :bands="event.bands" />-->
        <pre class="mt-8 text-pretty text-center">{{ event.content }}</pre>
        <p class="text-center text-orange">{{ removePostalCode(event.location) }}</p>
        <GoogleMap class="mt-5" v-if="event.cordinates" :cordinates="event.cordinates" />
    </GuestLayout>
</template>
