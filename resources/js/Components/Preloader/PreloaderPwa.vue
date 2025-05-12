<script setup>
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const visible = ref(false);

function isPwaMode() {
    return window.matchMedia('(display-mode: standalone)').matches || navigator.standalone;
}

onMounted(() => {
    router.on('start', () => {
        if (isPwaMode()) {
            visible.value = true;
            console.log('[PWA] navigation started');
        }
    });

    router.on('finish', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        visible.value = false;
    });
});
</script>

<template>
    <div v-if="visible" class="pwa-loader">
        <div class="spinner"></div>
        <p class="text-white text-2xl">Loading...</p>
    </div>
</template>


<style scoped>
.pwa-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    background-color: rgba(24, 24, 24, 0.8);
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 99999;
}

.spinner {
    width: 60px;
    height: 60px;
    border: 6px solid #ddd;
    border-top-color: #FF5722;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.pwa-loader p {
    margin-top: 16px;
    font-size: 1.2rem;
    color: #333;
}
</style>
