<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { computed } from 'vue';

const props = defineProps({
    start_date: {
        type: String
    },
    start_time: {
        type: String
    },
    flow: {
        type: Array,
        default: () => ['calendar', 'time']
    }
});

const emit = defineEmits(['update:start_date', 'update:start_time']);

const def = {};

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

    if (useTime.value) {
        emit('update:start_time', time);
        return `Selected date/time ${day}/${month}/${year} ${time}`;
    }
    return `Selected date ${day}/${month}/${year}`;
};

const useTime = computed(() => {
    return props.flow.includes('time');
});
</script>

<template>
    <div class="h-[346px] max-h-[360px] picker" :class="{ useTime }">
        <VueDatePicker :format="format" :flow="flow" dark inline />
    </div>
</template>
<style scoped lang="scss">
.picker {
    :deep(.dp__main) {
        position: relative;
        display: block !important;


        .dp__menu {
            max-height: 360px !important;
            height: 346px !important;

            .dp__instance_calendar {
                position: unset !important;
            }
        }

        .dp__cell_inner {
            &:hover {
                background-color: theme('colors.orange');
            }

            &.dp__active_date {
                background-color: theme('colors.red');
            }
        }

        .dp__action_buttons {
            display: none;
        }

        .dp--tp-wrap {
            .dp__btn {
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                display: none;

                .dp__overlay {
                    z-index: 100 !important;
                }
            }

        }
    }

    &.useTime {
        :deep(.dp__main) {
            .dp--tp-wrap .dp__btn {
                display: block;
                margin: auto;
                width: 40px;
                background-color: rgb(38 38 38 / var(--tw-bg-opacity, 1)) !important;
            }
        }
    }
}
</style>
