<script setup>
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const showPrompt = ref(false);
const showIosPrompt = ref(false);
let deferredEvent = null;

const isInstalled = () => {
    return window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone;
};

const isIOS = () => {
    return [
      'iPad Simulator',
      'iPhone Simulator',
      'iPod Simulator',
      'iPad',
      'iPhone',
      'iPod'
    ].includes(navigator.platform)
    || (navigator.userAgent.includes("Mac") && "ontouchend" in document);
};

onMounted(() => {
    if (isInstalled() || localStorage.getItem('pwaDismissed')) return;

    if (isIOS()) {
        setTimeout(() => showIosPrompt.value = true, 1000);
    } else {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredEvent = e;
            setTimeout(() => showPrompt.value = true, 1000);
        });
    }
});

const install = async () => {
    if (!deferredEvent) return;
    deferredEvent.prompt();
    const { outcome } = await deferredEvent.userChoice;
    if (outcome === 'accepted') {
        router.post(route('pwa.install'));
        localStorage.setItem('pwaInstalled', 'true');
        // router.visit('/events'); // Перенаправляем сразу после установки
    }
    close();
};

const dismiss = () => {
    localStorage.setItem('pwaDismissed', 'true');
    close();
};

const close = () => {
    showPrompt.value = false;
    showIosPrompt.value = false;
    deferredEvent = null;
};
</script>
<template>
    <div
        v-if="showPrompt || showIosPrompt"
        style="
      position: fixed;
      bottom: 1rem;
      right: 1rem;
      z-index: 50;
      background: #111;
      border: 1px solid #333;
      border-radius: 0.5rem;
      padding: 0.75rem;
      display: flex;
      gap: 0.75rem;
      max-width: 20rem;
    "
    >
        <img
            src="/icons/icon-192.png"
            style="
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.25rem;
      "
            alt="Rocker.am"
        >
        <div>
            <p style="
        font-size: 0.875rem;
        font-weight: 500;
        color: white;
        margin: 0 0 0.5rem 0;
        letter-spacing: 0;
      ">
                <span v-if="showIosPrompt">Install Rocker.am: tap Share icon below and select "Add to Home Screen"</span>
                <span v-else>Install Rocker.am events</span>
            </p>
            <div style="
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
      ">
                <button
                    @click="dismiss"
                    style="
            color: #999;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            letter-spacing: 0;
            background: none;
            border: none;
            cursor: pointer;
          "
                >
                    <span v-if="showIosPrompt">Close</span>
                    <span v-else>Later</span>
                </button>
                <button
                    v-if="!showIosPrompt"
                    @click="install"
                    style="
            background: #dc2626;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            letter-spacing: 0;
            border: none;
            cursor: pointer;
          "
                >
                    Install
                </button>
            </div>
        </div>
    </div>
</template>
