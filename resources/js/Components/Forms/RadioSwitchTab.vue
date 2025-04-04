<script setup>
const props = defineProps({
    name: {
        type: String,
        required: false
    },
    field: {
        type: String,
        required: true
    },
    options: {
        type: Object,
        required: true
    },
});
</script>

<template>
    <div>
        <div class="flex justify-between items-center">
            <label :for="field"
                   class="cursor-pointer">{{ name }}</label>
            <div class="mt-normal-garden">
                <input :id="field" type="checkbox" v-model="options[field]"
                       :false-value="0"
                       :true-value="1"/>
                <label :for="field" class="scale-75"></label>
            </div>
        </div>
        <div v-if="options[field]" class="my-5">
            <slot/>
        </div>
    </div>
</template>
