<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout :meta="{title: '404 page not found'}">
        <template #header><span>404 page not found </span></template>
        <div class="flex justify-center items-center">
            <img src="/images/404.gif" alt="404 page not found" />
        </div>
        <div class="text-center">
            <h2>We lost this page</h2>
            <p> We searched high and low but couldn’t find what you’re looking for. Let’s find a better place for you to
                go.</p>
        </div>
    </GuestLayout>
</template>
