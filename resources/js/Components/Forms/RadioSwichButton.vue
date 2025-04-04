<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    options: {
        type: Array,
        required: false
    },
    selectedOption: {
        required: false
    },
    field: {
        type: String,
        required: false
    },
    indicate: {
        type: Boolean,
        required: false
    }
});

const emit = defineEmits(['update:selectedOption']);

const select = (value) => {
    emit('update:selectedOption', value);
};
const indicateCountry = (value) => {
    router.put(route('user.settings', { [props.field]: value, ...tokenData }));
};
</script>

<template>
    <div class="flex justify-between gap-x-1">
        <button v-for="option in options" :key="option"
                @click="indicate ? indicateCountry(option.key): select(option.key)"
                class="w-1/2 bg-dark-grey px-5 py-2 border-1 border-gray  rounded-lg text-sm font-medium text-white hover:bg-orange"
                :class="selectedOption === option.key ? 'bg-orange text-white' : 'bg-graydark2' "
                type="button">
            {{ option.name }}
        </button>
    </div>
</template>

<style scoped lang="css">
button {
    text-transform: lowercase;

    &::first-letter {
        text-transform: capitalize;
    }
}
</style>
