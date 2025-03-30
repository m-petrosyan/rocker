<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    events: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <GuestLayout>
        {{ events }}
    </GuestLayout>
</template>
