<script setup>
import { computed } from 'vue';
import GoogleLogoIcon from '@/Components/Icons/GoogleLogoIcon.vue';

const googleAuthUrl = computed(() => route('auth.google'));
const googleAuth = () => {
    window.location.href = googleAuthUrl.value;
};
</script>

<template>
    <GoogleLogoIcon
        @click="googleAuth"
        class="mx-auto cursor-pointer grayscale transition-all duration-300 hover:grayscale-0"
    />
</template>
