<script setup>

import UrlAddIcon from '@/Components/Icons/UrlAddIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    maxLinks: {
        type: Number,
        default: 3
    },
    tooltip: {
        type: String
    }
});

const emit = defineEmits(['update:data']);

const addLinks = () => {
    props.data.push({
        url: ''
    });

    emit('update:data', data);
};

const delLink = (index) => {
    props.data.splice(index, 1);
    emit('update:data', data);
};
</script>

<template>
    <div v-if="data.length" class="flex flex-col gap-y-2">
        <div v-for="(url, index) in data" class="flex gap-x-1">
            <input
                class="w-full bg-graydark2"
                type="text"
                v-model="data[index].url"
                placeholder="Url"
            />
            <button type="button" class="bg-red px-2 text-2xl bg-opacity-40 hover:bg-opacity-100"
                    @click="delLink(index)">
                <DeleteIcon size="22px" />
            </button>
        </div>
    </div>
    <button :tooltip="tooltip" :disabled="data.length >= maxLinks"
            type="button" @click="addLinks"
            class="bg-green w-fit p-2">
        <UrlAddIcon />
    </button>
</template>
