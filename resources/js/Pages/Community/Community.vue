<script setup>

import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout :meta="{ title: 'Community' }">
        <template #header> Community</template>
        <div class="flex">
            <div class="w-1/2">
                <h1 class="text-2xl font-bold">Community</h1>
                <p class="mt-4">Welcome to our community page! Here you can find all the latest news and events
                    happening in our community.</p>
            </div>
            <div class="w-1/2">
                <img src="/images/community.jpg" alt="Community Image" class="w-full h-auto" />
            </div>
        </div>
    </GuestLayout>
</template>
