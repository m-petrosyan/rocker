<script setup>
import { Head } from '@inertiajs/vue3';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';

defineProps({
    meta: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <!--    <Head title="sasd asds a" />-->
    <Head :title="meta.title" />
    {{ meta.title }}
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <MainNavbar />
        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
