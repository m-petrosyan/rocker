<script setup>
import GoogleCalendarIcon from '@/Components/Icons/GoogleCalendarIcon.vue';
import { date_time_utc, formatDateTime } from '@/Helpers/dateFormatHelper.js';

const props = defineProps({
  event: {
    type: Object,
    required: true
  }
});

const dateLocal = formatDateTime(
  props.event.date_time,
  'DD.MM.YY HH:mm',
  false
);

const [dateShort, timeShort] = dateLocal.split(' ');

const date = date_time_utc(dateShort, timeShort);

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
  {{ date }}
  <button @click="addToGoogleCalendar">
    <GoogleCalendarIcon class="saturate-50 hover:saturate-100" />
  </button>
</template>
