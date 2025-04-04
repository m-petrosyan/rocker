<script setup>
const props = defineProps({
    messages: Object
});
</script>

<template>
    <div v-if="Object.keys(messages).length">
        <div class="p-4 text-sm rounded-lg bg-red text-white text-wrap" role="alert">
            <ul v-for="error in messages " :key="error">
                <li>{{ error }}</li>
            </ul>
        </div>
    </div>
</template>
