<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    auth: {
        object: true
    }
});

</script>

<template>
    <GuestLayout :meta="{title: 'Home'}">
        <div class="p-4">
            {{ auth }}
        </div>
    </GuestLayout>
</template>
