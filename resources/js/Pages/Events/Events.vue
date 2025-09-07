<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import MegaMaps from '@/Components/Maps/MegaMaps.vue';

defineProps({
    events: {
        type: Object
    },
    auth: {
        type: Object
    }
});
</script>

<template>
    <GuestLayout
        :meta="{ title: 'Events', keywords: 'armenian rock events, armenian metal events, armenian rock concerts, armenian metal concerts, armenian rock festivals, armenian metal festivals, yerevan rock events, yerevan metal concerts, yerevan rock concerts, yerevan metal concerts, armenian live rock, armenian live metal, armenian concert events, armenian rock events calendar, armenian metal events calendar, armenian music events, armenian underground concerts, armenian underground music events, armenian concert announcements, armenian rock show announcements, armenian metal show announcements, armenian rock gigs, armenian metal gigs, armenian live music shows, yerevan concert events' + events.data.map(event => event.title).join(', ') }">
        <template #header> Events</template>
        <template #h1> Upcoming Armenian Rock & Metal Concerts and Events
        </template>
        <MegaMaps
            v-if="auth?.isAdmin"
            :markers="[
                        { latitude: 40.188067, longitude: 44.5139577, info: 'Tonlab' },
                        {
                            latitude: 40.1800023,
                            longitude: 44.5160956,
                            info: 'The venue',
                        },
                        {
                            latitude: 40.1895131,
                            longitude: 44.521072,
                            info: 'Stop club',
                        },
                        {
                            latitude: 40.1800023,
                            longitude: 44.5160956,
                            info: 'Tonlab2',
                        },
                        {
                            latitude: 40.1737167,
                            longitude: 44.516279399999995,
                            info: 'Hardrock',
                        },
                    ]"
        />
        <!--        <MultiSelect class="w-48" />-->
        <EventWrapper :events="events.data" add />
    </GuestLayout>
</template>
