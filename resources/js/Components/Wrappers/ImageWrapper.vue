<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import JSZip from 'jszip';
import { saveAs } from 'file-saver';
import DownloadIcon from '@/Components/Icons/DownloadIcon.vue';

const props = defineProps({
    images: {
        type: Array,
        required: true
    }
});

const selectedImageIndex = ref(null);

const currentImage = computed(() => {
    return selectedImageIndex.value !== null ? props.images[selectedImageIndex.value] : null;
});

const openModal = (index) => {
    selectedImageIndex.value = index;
};

const closeModal = () => {
    selectedImageIndex.value = null;
};

const prevImage = () => {
    if (selectedImageIndex.value !== null) {
        selectedImageIndex.value =
            (selectedImageIndex.value - 1 + props.images.length) % props.images.length;
    }
};

const nextImage = () => {
    if (selectedImageIndex.value !== null) {
        selectedImageIndex.value = (selectedImageIndex.value + 1) % props.images.length;
    }
};

const downloadImage = () => {
    if (currentImage.value && currentImage.value.original) {
        const link = document.createElement('a');
        link.href = currentImage.value.original;
        link.download = currentImage.value.original.split('/').pop() || 'image';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

const downloadAllImages = async () => {
    const zip = new JSZip();
    const folder = zip.folder('gallery');

    try {
        for (let i = 0; i < props.images.length; i++) {
            const image = props.images[i];
            if (image.original) {
                const response = await fetch(image.original);
                const blob = await response.blob();
                const filename = image.original.split('/').pop() || `image-${i + 1}`;
                folder.file(filename, blob);
            }
        }
        folder.generateAsync({ type: 'blob' }).then((content) => {
            saveAs(content, 'gallery-images.zip');
        });
    } catch (error) {
        console.error('Error creating ZIP:', error);
        alert('Failed to create ZIP file. Check the console for details.');
    }
};

const handleKeydown = (event) => {
    if (!currentImage.value) return;
    if (event.key === 'ArrowLeft') {
        prevImage();
    } else if (event.key === 'ArrowRight') {
        nextImage();
    } else if (event.key === 'Escape') {
        closeModal();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div class="relative pt-10 mt-5">
        <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-5">
            <div
                v-if="props.images.length"
                v-for="(image, index) in props.images"
                :key="index"
                class="aspect-square overflow-hidden relative cursor-pointer"
                @click="openModal(index)"
            >
                <img v-if="image.thumb && image.thumb.trim()"
                     :src="image.thumb"
                     class="w-full h-full object-cover object-center rounded-md"
                     alt="Loading image"
                     @error="$event.target.src = image.original" />
                <img v-else-if="image.original"
                     :src="image.original"
                     class="w-full h-full object-cover object-center rounded-md"
                     alt="Loading image" />

            </div>
        </div>
        <button
            class="flex gap-x-2 items-center font-bold absolute top-0 right-0 text-white rounded-lg p-2"
            @click="downloadAllImages"
            title="Download all images as ZIP"
        >
            Download
            <DownloadIcon />
        </button>
        <div
            v-if="currentImage"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
            @click.self="closeModal"
        >
            <div class="relative w-full h-full flex items-center justify-center">
                <img
                    :src="currentImage.large"
                    class="max-w-[100vw] max-h-[100vh] object-contain"
                    alt="Uploading"
                />

                <button
                    class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-2"
                    @click="closeModal"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                </button>

                <button
                    v-if="props.images.length > 1"
                    class="absolute left-2 top-1/2 transform -translate-y-1/2 text-white bg-black bg-opacity-50 rounded-full p-2"
                    @click="prevImage"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        ></path>
                    </svg>
                </button>

                <button
                    v-if="props.images.length > 1"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white bg-black bg-opacity-50 rounded-full p-2"
                    @click="nextImage"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        ></path>
                    </svg>
                </button>

                <button
                    class="absolute top-2 right-10 text-white bg-opacity-75 rounded-full p-2"
                    @click="downloadImage"
                >
                    <DownloadIcon />
                </button>
            </div>
        </div>
    </div>
</template>
