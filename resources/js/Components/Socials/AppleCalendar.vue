<script setup>
import { toIso8601WithEnd } from '@/Helpers/dateFormatHelper.js';
import Icalcon from '@/Components/Icons/Icalcon.vue';

const props = defineProps({
  event: {
    type: Object,
    required: true
  }
});

const date = toIso8601WithEnd(props.event.start_date, props.event.start_time);

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

  const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
  const url = URL.createObjectURL(blob);

  const iframe = document.createElement('iframe');
  iframe.style.display = 'none';
  iframe.src = url;
  document.body.appendChild(iframe);

  setTimeout(() => {
    document.body.removeChild(iframe);
    URL.revokeObjectURL(url);
  }, 1000);
};
</script>

<template>
  <button @click="addToICalendar">
    <Icalcon class="saturate-50 hover:saturate-100" />
  </button>
</template>
