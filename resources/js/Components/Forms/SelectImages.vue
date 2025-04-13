<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useDropZone } from '@vueuse/core';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import ImageIcon from '@/Components/Icons/ImageIcon.vue';

const props = defineProps({
    previews: { type: Array, required: true },
    files: { type: Object, required: true },
    classes: { type: String, default: '' }
});

const emit = defineEmits(['update:previews', 'update:files']);
const form = useForm({});
const dropZoneRef = ref(null);
const isLoading = ref(false);

const removeImage = (index) => {
    const newPreviews = [...props.previews];
    newPreviews.splice(index, 1);
    emit('update:previews', newPreviews);

    const dataTransfer = new DataTransfer();
    const filesArray = Array.from(props.files);
    filesArray.forEach((file, i) => {
        if (i !== index) {
            dataTransfer.items.add(file);
        }
    });
    emit('update:files', dataTransfer.files);
};

const uploadImages = (event) => {
    const files = event.target.files;
    if (files) processFiles(files);
};

const processFiles = async (files) => {
    isLoading.value = true;
    const fileList = Array.from(files);
    const chunkSize = 10;
    const newPreviews = [...props.previews];

    const dataTransfer = new DataTransfer();
    fileList.forEach(file => dataTransfer.items.add(file));
    emit('update:files', dataTransfer.files);

    for (let i = 0; i < fileList.length; i += chunkSize) {
        const chunk = fileList.slice(i, i + chunkSize);

        for (const file of chunk) {
            const url = await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });
            newPreviews.push(url);
            emit('update:previews', [...newPreviews]);
        }

        await new Promise((resolve) => setTimeout(resolve, 50));
    }

    isLoading.value = false;
};

const onDrop = (files) => {
    if (files) {
        const validFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
        if (validFiles.length) processFiles(validFiles);
    }
};

const { isOverDropZone } = useDropZone(dropZoneRef, {
    onDrop,
    dataTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
    multiple: true
});

const dropZoneClass = computed(() =>
    isOverDropZone.value
        ? 'border-orange bg-graydark2'
        : 'border-gray'
);
</script>

<template>
    <div :class="classes">
        <div v-if="isLoading" class="flex items-center justify-center h-64">
            <span class="text-2xl text-gray-500">Loading...</span>
        </div>

        <div v-else>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-5">
                <div
                    v-if="previews.length"
                    v-for="(preview, index) in previews"
                    :key="index"
                    class="aspect-square overflow-hidden relative"
                >
                    <img :src="preview" class="w-full h-full object-cover object-center" alt="Image" />
                    <button type="button" class="absolute top-0 left-0 z-10 p-2" @click="removeImage(index)">
                        <ImageIcon class="opacity-50 hover:opacity-100" />
                    </button>
                    <button type="button" class="absolute bottom-0 right-0 z-10 p-2" @click="removeImage(index)">
                        <DeleteIcon class="opacity-50 hover:opacity-100" />
                    </button>
                </div>
            </div>

            <div class="relative mt-10">
                <input
                    type="file"
                    hidden
                    id="files"
                    accept="image/*"
                    @change="uploadImages"
                    multiple
                />
                <label
                    ref="dropZoneRef"
                    for="files"
                    :class="[
                        'w-full h-96 border-2 border-dashed flex items-center justify-center transition-all duration-200 cursor-pointer',
                        dropZoneClass,
                    ]"
                >
                    Click or drag files here
                </label>
            </div>
        </div>
    </div>
</template>

<style scoped>
.images {
    max-width: 100%;
    height: auto;
}
</style>
