<script setup>
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const showFlash = ref(false);
const message = ref('');

const handleFlashMessage = (newMessage) => {
    if (newMessage) {
        message.value = newMessage;

        showFlash.value = true;
        setTimeout(() => {
            showFlash.value = false;
            message.value = '';
        }, 5000);
    }
};

watch(
    () => page.props.flash?.message,
    handleFlashMessage,
    { immediate: true, deep: true }
);

onMounted(() => {
    handleFlashMessage(page.props.flash?.message);
});

</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform translate-x-full opacity-0"
        enter-to-class="transform translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-x-0 opacity-100"
        leave-to-class="transform translate-x-full opacity-0">
        <div
            v-if="showFlash"
            class="fixed z-20 text-white top-4 right-2 flex items-center bg-orange text-sm font-bold px-4 py-3 rounded shadow-lg"
            role="alert">
            <svg
                class="fill-current w-4 h-4 mr-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20">
                <path
                    d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
            </svg>
            <p>{{ message }}</p>
            <button
                @click="showFlash = false"
                class="ml-4 text-white hover:text-gray-100 focus:outline-none">
                <svg
                    class="h-4 w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>
    </Transition>
</template>

<style scoped>

</style>
