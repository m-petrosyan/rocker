<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useDropZone } from '@vueuse/core';
import Compressor from 'compressorjs';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import ImageIcon from '@/Components/Icons/ImageIcon.vue';

const props = defineProps({
    useCover: { type: Boolean, default: false },
    cover: { type: Number, default: false },
    previews: { type: Array, required: true },
    files: { type: Object, required: true },
    classes: { type: String, default: '' },
    limit: { type: Number, default: 0 },
    label: { type: String, default: 'Click or drag files here' }
});

const form = useForm({});
const emit = defineEmits(['update:previews', 'update:files', 'update:cover']);
const dropZoneRef = ref(null);
const isLoading = ref(false);

const removeImage = (index, id) => {
    if (id) {
        if (confirm('Are you sure you want to delete this image?')) {
            form.delete(route('profile.media.destroy', id), {
                preserveState: false,
                preserveScroll: true
            });
        }
    } else {
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
    }
};

const uploadImages = (event) => {
    const files = event.target.files;
    if (files) processFiles(files);
};

const compressForPreview = (file) => {
    return new Promise((resolve) => {
        new Compressor(file, {
            quality: 1,
            maxWidth: 210,
            maxHeight: 210,
            mimeType: 'image/jpeg',
            success: (compressedFile) => resolve(compressedFile),
            error: () => resolve(file)
        });
    });
};

const processFiles = async (files) => {
    isLoading.value = true;
    const fileList = Array.from(files);

    // Вычисляем оставшуюся емкость с учетом лимита
    const remainingCapacity = props.limit > 0 ? props.limit - props.previews.length : fileList.length;

    // Если нет свободного места, выводим предупреждение
    if (props.limit > 0 && remainingCapacity <= 0) {
        alert(`Вы достигли максимального лимита в ${props.limit} изображений.`);
        isLoading.value = false;
        return;
    }

    // Ограничиваем количество обрабатываемых файлов
    const filesToProcess = fileList.slice(0, remainingCapacity);
    const newPreviews = [...props.previews];

    // Создаем новый DataTransfer, включая существующие файлы
    const dataTransfer = new DataTransfer();

    // Добавляем существующие файлы из props.files
    if (props.files && props.files.length) {
        Array.from(props.files).forEach(file => dataTransfer.items.add(file));
    }

    // Добавляем новые файлы
    filesToProcess.forEach(file => dataTransfer.items.add(file));
    emit('update:files', dataTransfer.files);

    // Обрабатываем файлы по частям
    const chunkSize = 5;
    for (let i = 0; i < filesToProcess.length; i += chunkSize) {
        const chunk = filesToProcess.slice(i, i + chunkSize);

        for (const file of chunk) {
            const compressedFile = await compressForPreview(file);
            const url = await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(compressedFile);
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

const setCover = (index) => {
    emit('update:cover', index);
};

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
            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                <div
                    v-if="previews.length"
                    v-for="(preview, index) in previews"
                    :key="index"
                    class="aspect-square relative"
                >
                    <img :src="preview.thumb ?? preview"
                         :class="{'border border-2 border-orange opacity-100 brightness-125': cover === preview.id || cover === index}"
                         class="w-full h-full object-cover object-center opacity-75" alt="Image" />
                    <div
                        class="absolute left-0 top-0 md:opacity-0 hover:opacity-100 flex flex-col justify-between w-full h-full z-10 p-2 bg-blackTransparent2">
                        <button v-if="useCover" type="button" class="w-fit"
                                @click="setCover(index)"
                                v-tooltip="'Set cover'">
                            <ImageIcon />
                        </button>
                        <div class="flex justify-end">
                            <button type="button" class="w-fit" @click="removeImage(index, preview.id)"
                                    v-tooltip="'Delete'">
                                <DeleteIcon />
                            </button>
                        </div>
                    </div>
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
                    {{ label }}
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
