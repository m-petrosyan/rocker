<script setup>
import { toIso8601WithEnd } from '@/Helpers/dateFormatHelper.js';
import Icalcon from '@/Components/Icons/Icalcon.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

// Генерация временных меток начала и окончания события
const date = toIso8601WithEnd('10.04.25', '15:00');

const addToGoogleCalendar = () => {
    // Формирование URL для создания события в Google Calendar
    const googleCalendarUrl =
        `https://www.google.com/calendar/render?action=TEMPLATE` +
        `&text=${encodeURIComponent(props.event.title)}` +
        `&dates=${date.start}/${date.end}` +
        `&details=${encodeURIComponent(props.event.content)}` +
        `&location=${encodeURIComponent(props.event.location)}`;

    // Открытие URL в новой вкладке
    window.open(googleCalendarUrl, '_blank');
};
</script>

<template>
    <button @click="addToGoogleCalendar">
        <Icalcon />
    </button>
</template>
