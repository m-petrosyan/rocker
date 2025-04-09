<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ProgressBar from 'primevue/progressbar';
import { computed, ref } from 'vue';

defineProps({
    galleries: {
        type: String
    }
});

const value = ref(50);
const limit = 200;

const setVal = () => {
    value.value = Math.floor(Math.random() * limit);
};

const percent = computed(() => {
    return (value.value / limit) * 100;
});
</script>

<template>
    <GuestLayout :meta="{title: 'Events'}">
        <ProgressBar class="w-full h-64 bg-green" :class="percent > 70 ? 'warning' : '' " :value="percent"> {{ value
            }}/{{ limit }}
        </ProgressBar>
        <button @click="setVal">
            {{ galleries }}
        </button>
    </GuestLayout>
</template>
