<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useTelegramAuth } from '@/Helpers/setAppUser.js';

defineProps({
    auth: {
        object: true
    }
});

useTelegramAuth();
</script>

<template>
    <GuestLayout :meta="{title: 'Home'}">
        <div class="p-4">
            {{ auth }}
        </div>
    </GuestLayout>
</template>
