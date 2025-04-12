<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    galleries: {
        type: String
    }
});
</script>

<template>
    <GuestLayout :meta="{title: 'Galleries'}">
        Soon
    </GuestLayout>
</template>
