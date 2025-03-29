<template>
    <div v-if="showInstallPrompt" class="fixed bottom-4 right-4 z-50">
        <div class="bg-gray-900 border border-gray-700 rounded-lg shadow-xl p-4 max-w-xs">
            <div class="flex items-start">
                <img
                    src="/icon-192.png"
                    class="w-12 h-12 mr-3 rounded"
                    alt="App icon"
                >
                <div>
                    <h3 class="font-bold text-white">Установить Rocker.am</h3>
                    <p class="text-gray-400 text-sm mt-1">
                        Добавьте приложение на главный экран для быстрого доступа
                    </p>
                    <div class="flex justify-end space-x-2 mt-3">
                        <button
                            @click="dismissPrompt"
                            class="px-3 py-1 text-gray-400 hover:text-white text-sm"
                        >
                            Позже
                        </button>
                        <button
                            @click="installApp"
                            class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-sm"
                        >
                            Установить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const showInstallPrompt = ref(false);
let deferredPrompt = null;


// Проверяем, можно ли показать промпт
onMounted(() => {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        // Показываем только если приложение еще не установлено
        if (!isRunningAsPWA()) {
            // Задержка для лучшего UX (чтобы не показывать сразу при загрузке)
            setTimeout(() => {
                showInstallPrompt.value = true;
            }, 10000); // 10 секунд задержки
        }
    });
});

// Проверка, установлено ли приложение
const isRunningAsPWA = () => {
    return window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone ||
        document.referrer.includes('android-app://');
};

// Установка приложения
const installApp = async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            console.log('User accepted install');
        }
        deferredPrompt = null;
        showInstallPrompt.value = false;
    }
};

// Скрытие промпта
const dismissPrompt = () => {
    showInstallPrompt.value = false;
    // Можно сохранить в localStorage, чтобы не показывать снова
    localStorage.setItem('pwaPromptDismissed', 'true');
};
</script>
