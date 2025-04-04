<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    start_date: {
        type: String
    },
    start_time: {
        type: String
    }
});

const emit = defineEmits(['update:start_date', 'update:start_time']);

const def = {
    flow: ['calendar', 'time']
};

const dateFormat = (value) => {
    return String(value).padStart(2, '0');
};

const format = (date) => {
    const day = dateFormat(date.getDate());
    const month = dateFormat(date.getMonth() + 1);
    const year = date.getFullYear();
    const hour = dateFormat(date.getHours());
    const minute = dateFormat(date.getMinutes());
    const time = hour + ':' + minute;
    emit('update:start_date', `${year}-${month}-${day}`);
    emit('update:start_time', `time`);
    return `Selected date is ${day}/${month}/${year} ${time}`;
};
</script>

<template>
    <div>
        <VueDatePicker :format="format" :flow="def.flow" dark inline />
    </div>
</template>
