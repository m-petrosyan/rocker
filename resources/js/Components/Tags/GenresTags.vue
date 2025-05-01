<script setup>

defineProps({
    genres: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-wrap gap-x-2 gap-y-2">
        <div
            v-for="genre in genres"
            class="bg-red px-1 rounded-sm">
            <div class="bg-red ">{{ genre.name }}</div>
        </div>
    </div>
</template>
