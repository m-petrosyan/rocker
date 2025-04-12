<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    message: String,
    timeout: Number,
    success: Boolean,
    error: Boolean,
    info: Boolean,
    warning: Boolean
});

const styles = computed(() => {
    if (props.success) return 'bg-green';
    if (props.error) return 'bg-red';
    if (props.info) return 'bg-grayblue';
    if (props.warning) return 'bg-yellow';
});

const show = ref(true);

onMounted(() => {
    if (props.timeout) {
        setInterval(() => show.value = false, props.timeout);
    }
});
</script>

<template>
    <div v-show="show">
        <div class="error p-4 mb-4 text-sm rounded-lg  text-white text-wrap" :class="styles" role="alert">
            <p v-if="message">{{ message }}</p>
            <slot />
        </div>
    </div>
</template>
