<script setup>
defineProps({
    items: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-wrap gap-x-2 gap-y-2">
        <div v-for="item of items" class="bg-red px-1 rounded-sm">
            <div class="bg-red ">{{ item.name }}</div>
        </div>
    </div>
</template>
