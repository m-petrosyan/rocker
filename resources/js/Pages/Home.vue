<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    phpVersion: {
        type: String,
        required: true
    }
});

</script>

<template>
    <GuestLayout :meta="{title: 'asdasd'}">
        hhhhhhhhh
    </GuestLayout>
</template>
