<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    message: String,
    timeout: Number
});

const show = ref(true);

onMounted(() => {
    if (props.timeout) {
        setInterval(() => show.value = false, props.timeout);
    }
});
</script>

<template>
    <div v-if="message" v-show="show">
        <div class="error p-4 mb-4 text-sm  rounded-lg bg-green text-white text-wrap" role="alert">
            <p>{{ message }}</p>
        </div>
    </div>
</template>
