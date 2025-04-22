<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    image: { type: Object, required: true },
    preview: { type: Object, required: true, default: '/images/noimage.jpg' },
    labelClass: { type: String, default: 'bg-cover' },
    label: { type: String, default: 'preview' },
    classes: { type: String, default: 'w-full' },
    blur: { type: Boolean, default: false }
});

const previewFile = ref(null);

const emit = defineEmits(['update:preview', 'update:file']);

const changePreview = (file) => {
    previewFile.value = URL.createObjectURL(file.target.files[0]);
    emit('update:preview', file.target.files[0]);
};

const previewStyle = computed(() => {
    const imageUrl = previewFile.value ?? props.image?.thumb ?? null;

    return imageUrl
        ? { backgroundImage: `url(${imageUrl})` }
        : {};
});
</script>

<template>
    <div :class="classes" class="relative bg-no-repeat bg-center"
         :style="previewStyle">
        <input type="file" hidden accept="image/*" :id="label"
               @change="changePreview">
        <label
            :class="['absolute h-full w-full z-10 flex h-inherit min-h-60 w-inherit items-center justify-center border-dashed border-2 border-graydark2 cursor-pointer rounded-md bg-no-repeat bg-center bg-contain rounded-10 mx-auto',labelClass]"
            :for="label"
            :style="blur ? previewStyle : {}">
            <span v-if="!Object.keys(previewStyle).length">Click to upload {{ label }}</span>
        </label>
        <div v-if="blur" class="absolute inset-0 backdrop-blur-md z-0 brightness-50"></div>
    </div>
</template>

<style scoped>

</style>
