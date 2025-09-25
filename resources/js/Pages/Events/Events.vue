<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import Pagination from '@/Components/Elemtns/Pagination.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
    events: {
        type: Object
    },
    isPast: {
        type: Boolean,
        default: false
    },
    auth: {
        type: Object
    }
});

</script>

<template>
    <GuestLayout
        :meta="{ title: 'Events - Armenian Rock & Metal Concerts & Events', keywords: 'armenian rock events, armenian metal events, armenian rock concerts, armenian metal concerts, armenian rock festivals, armenian metal festivals, yerevan rock events, yerevan metal concerts, yerevan rock concerts, yerevan metal concerts, armenian live rock, armenian live metal, armenian concert events, armenian rock events calendar, armenian metal events calendar, armenian music events, armenian underground concerts, armenian underground music events, armenian concert announcements, armenian rock show announcements, armenian metal show announcements, armenian rock gigs, armenian metal gigs, armenian live music shows, yerevan concert events' + events.data.map(event => event.title).join(', ') }">
        <template #header> Events</template>
        <template #h1>
            {{ isPast
            ? 'Past Armenian Rock & Metal Concerts and Events Archive'
            : 'Upcoming Armenian Rock & Metal Concerts and Events'
            }}
        </template>
        <p>
            Armenian rock and metal concerts are a vibrant part of the local scene.
            This page features
            {{ isPast ? 'past Armenian rock & metal concerts and events' : 'upcoming Armenian rock & metal concerts and events'
            }}
            in Yerevan and beyond, collected in one place for quick discovery.
        </p>
        <EventWrapper :events="events.data" v-bind:add="!isPast" />
        <Pagination :links="events.links" />
        <NavLink
            v-if="!isPast"
            :href="route('events.past',  { page: 1 })"
            class="flex flex-col items-center p-4">
            Past events
        </NavLink>
        <p class="px-4 pt-10">
            Check listings for dates, venues, and band details.
            Use this guide to revisit standout performances in the archive or plan your next night out at a live show.
            Stay tuned so you never miss key Armenian rock and metal events.
        </p>
    </GuestLayout>
</template>
