<script setup>
import { onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const visible = ref(false);
const page = usePage();

// Show loader on Inertia navigation start, hide on finish
onMounted(() => {
    page.events.listen('start', () => {
        visible.value = true;
    });
    page.events.listen('finish', () => {
        visible.value = false;
    });
});
</script>

<template>
    <p class="text-white"> {{ visible }}</p>
    <div v-if="false" class="pwa-loader">
        <div class="spinner"></div>
        <p>Loading...</p>
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
