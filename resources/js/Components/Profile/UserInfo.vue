<script setup>
import QrGenerate from '@/Components/Profile/QrGenerate.vue';

defineProps({
    name: {
        type: Object,
        required: true
    },
    url: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="absolute left-1/2 top-[-80px] w-fit -translate-x-1/2 text-center">
        <div class="relative mx-auto w-fit">
            <img
                src="/images/user.jpg"
                alt="Profile Picture"
                class="mx-auto mt-6 h-32 w-32 rounded-full border-4 border-white object-cover"
            />
            <QrGenerate v-if="url" :url />
        </div>
        <h3 class="text-gray-900 p-6">
            {{ name }}
        </h3>
    </div>
</template>
