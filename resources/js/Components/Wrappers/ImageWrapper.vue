<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import JSZip from 'jszip';
import { saveAs } from 'file-saver';
import DownloadIcon from '@/Components/Icons/DownloadIcon.vue';
import SocialShare from '@/Components/Socials/SocialShare.vue';
import Preloader from '@/Components/Preloader/Preloader.vue';
import { emitter } from '@/Helpers/event-bus.js';

const props = defineProps({
  title: { type: String, default: '' },
  url: { type: String, default: '' },
  images: { type: Array, required: true },
  download: { type: Boolean, default: false },
  classes: { type: String, default: 'grid grid-cols-3 md:grid-cols-6 gap-2 mt-5' }
});

const selectedImageIndex = ref(null);
const isLoading = ref(false); // Новое состояние для прелоадера

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

const downloadImage = (img = currentImage.value) => {
  if (img && img.original) {
    const link = document.createElement('a');
    link.href = img.original;
    link.download = img.original.split('/').pop() || 'image';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
};

const downloadAllImages = async () => {
  isLoading.value = true; // Включаем прелоадер
  const zip = new JSZip();
  const folder = zip.folder('gallery');

  try {
    emitter.emit('preloader-toggle', true);

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
      saveAs(content, 'rocker-images.zip');
    });
  } catch (error) {
    console.error('Error creating ZIP:', error);
  } finally {
    isLoading.value = false;
    emitter.emit('preloader-toggle', false);
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
    <div v-if="download" class="flex justify-between items-center">
      <SocialShare :title :url />
      <button
        class="flex gap-x-2 items-center font-bold text-white bg-orange hover:bg-orange-600 transition-colors duration-200 rounded-lg px-4 py-2"
        @click="downloadAllImages"
        title="Download all images as ZIP"
        :disabled="isLoading"
      >
        <span v-if="isLoading">Downloading...</span>
        <span v-else>Download all ({{ images.length }} photos)</span>
        <DownloadIcon />
      </button>
    </div>

    <Preloader v-if="isLoading" />

    <div :class="classes">
      <div
        v-if="images.length"
        v-for="(image, index) in images"
        :key="index"
        class="aspect-square overflow-hidden relative cursor-pointer group"
        @click="openModal(index)"
      >
        <img
          v-if="image.thumb && image.thumb.trim()"
          :src="image.thumb"
          class="w-full h-full object-cover object-center rounded-md"
          :alt="title"
          @error="$event.target.src = image.original"
        />
        <img
          v-else-if="image.original"
          :src="image.original"
          class="w-full h-full object-cover object-center rounded-md"
          :alt="title"
        />

        <button
          v-if="download"
          class="absolute top-2 right-2 p-2 rounded-full bg-black bg-opacity-50 text-white opacity-0 group-hover:opacity-100 hover:bg-orange hover:opacity-100 transition-all duration-300"
          @click.stop="downloadImage(image)"
          title="Download image"
        >
          <DownloadIcon />
        </button>
      </div>
    </div>

    <div
      v-if="currentImage"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="relative w-full h-full flex items-center justify-center">
        <img
          :src="currentImage.large"
          class="max-w-[100vw] max-h-[100vh] object-contain"
          :alt="title"
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

        <button v-if="download"
                class="absolute top-2 right-14 text-white bg-black bg-opacity-50 hover:bg-orange transition-colors duration-200 rounded-full p-2"
                @click="downloadImage"
                title="Download image"

        >
          <DownloadIcon />
        </button>
      </div>
    </div>
  </div>
</template>
