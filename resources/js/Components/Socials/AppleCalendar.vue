<script setup>
import { toIso8601WithEnd } from '@/Helpers/dateFormatHelper.js';
import Icalcon from '@/Components/Icons/Icalcon.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

// Генерация времени начала и окончания события
const date = toIso8601WithEnd('10.04.25', '15:00');

const generateIcsContent = () => {
    const icsContent = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//armcamp.com//EN',
        'CALSCALE:GREGORIAN',
        'BEGIN:VEVENT',
        `DTSTART:${date.start}`,
        `DTEND:${date.end}`,
        `SUMMARY:${props.event.title}`,
        `DESCRIPTION:${props.event.content}`,
        `LOCATION:${props.event.location}`,
        'END:VEVENT',
        'END:VCALENDAR'
    ].join('\r\n');


    fetch('/save-event', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ icsContent })
    });
};
</script>

<template>
    <button @click="generateIcsContent">
        <Icalcon />
    </button>
</template>
