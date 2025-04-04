<script setup>
import { useQRCode } from '@vueuse/integrations/useQRCode';
import { ref } from 'vue';
import QrIcon from '@/Components/Icons/QrIcon.vue';

const text = ref(window.location.href);
const qrCode = useQRCode(text);
const showQr = ref(false);
</script>

<template>
    <div>
        <Transition name="fade">
            <div
                v-show="showQr"
                @click="showQr = !showQr"
                class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 z-50"
            >
                <Transition name="scale">
                    <img
                        v-show="showQr"
                        :src="qrCode"
                        class="border-2 rounded-xl"
                        alt="QR Code"
                    >
                </Transition>
            </div>
        </Transition>

        <!-- Кнопка -->
        <button
            @click="showQr = !showQr"
            class="absolute bottom-0 right-0 bg-black bg-opacity-50"
        >
            <QrIcon />
        </button>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    @apply transition-opacity duration-500 ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
    @apply opacity-0;
}

.fade-enter-to,
.fade-leave-from {
    @apply opacity-100;
}

.scale-enter-active,
.scale-leave-active {
    @apply transition-transform duration-500 ease-in-out;
}

.scale-enter-from,
.scale-leave-to {
    @apply scale-0;
}

.scale-enter-to,
.scale-leave-from {
    @apply scale-100;
}
</style>
