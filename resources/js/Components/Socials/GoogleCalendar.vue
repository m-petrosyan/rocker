<script setup>
import GoogleCalendarIcon from '@/Components/Icons/GoogleCalendarIcon.vue';
import { date_time_utc } from '@/Helpers/dateFormatHelper.js';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

const date = date_time_utc(props.event.start_date, props.event.start_time);

const addToGoogleCalendar = () => {
    const title = encodeURIComponent(props.event.title);
    const details = encodeURIComponent(props.event.content);
    const location = encodeURIComponent(props.event.location);
    const start = date.utcTime;
    const end = date.endHourUtc;

    const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${start}/${end}`;

    window.open(url, '_blank');
};
</script>

<template>
    <button @click="addToGoogleCalendar">
        <GoogleCalendarIcon />
    </button>
</template>
