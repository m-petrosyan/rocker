<script setup>
import { computed, ref } from 'vue';
import ArrowUpDown from '@/Components/Icons/ArrowUpDown.vue';

const props = defineProps({
    image: { type: Object, required: true },
    preview: { type: Object, required: true, default: '/images/noimage.jpg' },
    labelClass: { type: String, default: 'bg-cover' },
    label: { type: String, default: 'preview' },
    classes: { type: String, default: 'w-full' },
    blur: { type: Boolean, default: false },
    backgroundPosition: { type: Object }
});

const previewFile = ref(null);
const backgroundPosition = ref(props.backgroundPosition);
const isDragging = ref(false);
let clickTimer = null;
const clickDuration = 200;

const emit = defineEmits(['update:preview', 'update:file']);

const changePreview = (file) => {
    previewFile.value = URL.createObjectURL(file.target.files[0]);
    emit('update:preview', file.target.files[0]);
};

const startInteraction = (event) => {
    if (event.button === 0) {
        event.preventDefault();
        clickTimer = setTimeout(() => {
            isDragging.value = true;
        }, clickDuration);
    }

};

const endInteraction = (event) => {
    if (isDragging.value) {
        isDragging.value = false;
    } else if (clickTimer) {
        const input = document.getElementById(props.label);
        input.click();
    }
    clearTimeout(clickTimer);
    clickTimer = null;
};

const dragImage = (event) => {

    if (!isDragging.value) return;

    const rect = event.currentTarget.getBoundingClientRect();
    const x = ((event.clientX - rect.left) / rect.width) * 100;
    const y = ((event.clientY - rect.top) / rect.height) * 100;

    backgroundPosition.value.x = Math.max(0, Math.min(100, x));
    backgroundPosition.value.y = Math.max(0, Math.min(100, y));
    emit('update:backgroundPosition', backgroundPosition.value);
};

const previewStyle = computed(() => {
    const imageUrl = previewFile.value ?? props.image?.svg ?? props.image?.large ?? null;

    return imageUrl
        ? {
            backgroundImage: `url(${imageUrl})`,
            backgroundPosition: `${props.backgroundPosition?.x}% ${props.backgroundPosition?.y}%`
        }
        : {};
});
</script>

<template>
    <div
        :class="classes"
        class="relative bg-no-repeat bg-center flex justify-center items-center rounded-md overflow-hidden group"
        :style="previewStyle"
        @mousedown="startInteraction"
        @mousemove="dragImage"
        @mouseup="endInteraction"
        @mouseleave="endInteraction"
    >
        <input type="file" hidden accept="image/*" :id="label" @change="changePreview">
        <label
            :class="[
                'absolute h-full w-full z-10 flex h-inherit min-h-60 w-inherit items-center justify-center border-dashed border-2 border-graydark2 cursor-pointer rounded-md bg-no-repeat bg-center bg-contain rounded-10 mx-auto',
                labelClass,(image || preview) && backgroundPosition ? 'cursor-n-resize' : ''
            ]"
            :style="blur ? previewStyle : {}"
        >
            <ArrowUpDown v-if="backgroundPosition && (image || preview)" class="hidden group-hover:block" />

            <span v-if="!Object.keys(previewStyle).length">Click to upload {{ label }}</span>
        </label>
        <div v-if="blur" class="absolute inset-0 backdrop-blur-md z-0 brightness-50"></div>
    </div>
</template>

<style scoped>
.relative {
    user-select: none;
}
</style>
