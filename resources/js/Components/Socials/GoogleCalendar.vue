<script setup>
import { date_time_utc } from '@/Helpers/dateFormatHelper.js';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

const addToGoogleCalendar = () => {
    const title = encodeURIComponent(props.event.title);
    const details = encodeURIComponent(props.event.content);
    const location = encodeURIComponent(props.event.location);
    const start = date_time_utc(props.event.start_date, props.event.start_time);

    const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${start}`;

    window.open(url, '_blank');
};
</script>


<template>
    <button @click="addToGoogleCalendar">
        Add to Google Calendar
    </button>
</template>

