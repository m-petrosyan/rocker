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

const addToICalendar = () => {
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

    // Создаем Blob с ICS-контентом
    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const url = URL.createObjectURL(blob);

    // Создаем скрытый iframe для попытки открытия ICS-файла напрямую
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);

    // Освобождаем ресурсы через короткий промежуток времени
    setTimeout(() => {
        document.body.removeChild(iframe);
        URL.revokeObjectURL(url);
    }, 1000);
};
</script>

<template>
    <button @click="addToICalendar">
        <Icalcon />
    </button>
</template>
