<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    active: {
        type: Boolean
    },
    label: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <Link :href="href" class="block" :aria-label="label">
        <slot />
    </Link>
</template>
