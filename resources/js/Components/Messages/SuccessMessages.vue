<script setup>
const props = defineProps({
    message: String
});
</script>

<template>
    <div v-if="message">
        <div class="error p-4 mb-4 text-sm  rounded-lg bg-green text-white text-wrap" role="alert">
            <p>{{ message }}</p>
        </div>
    </div>
</template>
