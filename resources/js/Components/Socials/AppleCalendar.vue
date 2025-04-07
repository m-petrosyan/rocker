<script setup>
import { toIso8601WithEnd } from '@/Helpers/dateFormatHelper.js';
import Icalcon from '@/Components/Icons/Icalcon.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

const date = toIso8601WithEnd('10.04.25', '15:00');

const downloadIcs = () => {
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

    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'event.ics';
    link.click();

    setTimeout(() => URL.revokeObjectURL(url), 1000);
};
</script>

<template>
    <button @click="downloadIcs">
        <Icalcon />
    </button>

</template>
