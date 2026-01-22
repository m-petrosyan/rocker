<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
  modelValue: {
    type: Array,
    default: null
  }
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
  <div class="picker">
    <VueDatePicker
      :model-value="modelValue"
      @update:model-value="val => emit('update:modelValue', val)"
      range
      dark
      inline
      auto-apply
      :enable-time-picker="false"
    />
  </div>
</template>

<style scoped lang="scss">
.picker {
  width: 100%;
  max-width: 320px;
  margin: 0 auto;

  :deep(.dp__main) {
    font-family: inherit;

    .dp__menu {
      background: transparent;
      border: none;
      padding: 0;
    }

    .dp__calendar_header {
      font-weight: 600;
      color: theme('colors.gray');
      font-size: 11px;
      text-transform: uppercase;
    }

    .dp__cell_inner {
      border-radius: 8px;
      font-size: 13px;
      height: 34px;
      width: 34px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      color: rgba(255, 255, 255, 0.8);

      &:hover {
        background-color: theme('colors.orange');
        color: white;
        transform: scale(1.1);
      }

      &.dp__active_date, &.dp__range_start, &.dp__range_end {
        background-color: theme('colors.red') !important;
        color: white !important;
        border-radius: 8px;
        z-index: 10;
      }

      &.dp__range_between {
        background-color: theme('colors.red') !important;
        opacity: 0.15;
        border-radius: 0;
        color: white;
      }

      &.dp__today {
        border: 1px solid theme('colors.orange');
        color: theme('colors.orange');
      }
      
      &.dp__cell_offset {
          opacity: 0.2;
      }
    }

    .dp__month_year_row {
      margin-bottom: 10px;
    }

    .dp__month_year_wrap {
      color: white;
      font-weight: bold;
      font-size: 14px;
    }

    .dp__calendar_wrap {
      padding: 0;
    }

    .dp__action_buttons {
      display: none;
    }
  }
}
</style>
