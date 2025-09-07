<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout :meta="{title: '404 page not found'}">
        <template #header><span class="hidden">404 page not found </span></template>
        <div class="flex justify-center items-center">
            <img src="/images/404.png" alt="404 page not found" />
        </div>
    </GuestLayout>
</template>
