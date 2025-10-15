<script setup>
import { useQRCode } from '@vueuse/integrations/useQRCode';
import { ref } from 'vue';
import QrIcon from '@/Components/Icons/QrIcon.vue';

const props = defineProps({
    url: String
});

const qrCode = useQRCode(props.url);
const showQr = ref(false);
</script>

<template>
    <div>
        <Transition name="fade">
            <div
                v-show="showQr"
                @click="showQr = !showQr"
                class="fixed left-0 top-0 z-50 flex h-full w-full items-center justify-center bg-black bg-opacity-50"
            >
                <Transition name="scale">
                    <img
                        v-show="showQr"
                        :src="qrCode"
                        class="rounded-xl border-2"
                        alt="QR Code"
                    />
                </Transition>
            </div>
        </Transition>

        <div>
            <button
                @click="showQr = !showQr"
                class="absolute bottom-0 right-0 bg-black bg-opacity-50"
            >
                <div tooltip="Profile qr">
                    <QrIcon />
                </div>
            </button>
        </div>
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
