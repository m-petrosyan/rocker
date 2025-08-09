<script setup>
import { usePDF, VuePDF } from '@tato30/vue-pdf';
import { ref } from 'vue';
import ZoomInIcon from '@/Components/Icons/ZoomInIcon.vue';
import ZoomOutIcon from '@/Components/Icons/ZoomOutIcon.vue';

const props = defineProps({
    file: {
        type: [Object, String],
        required: true
    }
});


const fileUrl = props.file instanceof File ? URL.createObjectURL(props.file) : props.file;
const { pdf, pages } = usePDF(fileUrl);

const scale = ref(100);
</script>

<template>
    <div class="bg-graydark2 flex items-center justify-between p-2 rounded-t-lg mt-20">
        <div>Pages: {{ pages }}</div>
        <div class="flex items-center justify-center gap-2">
            <button @click.prevent="scale = scale < 100 ? scale + 25 : scale">
                <ZoomInIcon />
            </button>
            <div class="mx-2">{{ scale }}%</div>
            <button @click.prevent="scale = scale > 50 ? scale - 25 : scale">
                <ZoomOutIcon />
            </button>
        </div>
        <div></div>
    </div>
    <div v-for="page in pages" :key="page" :style="{ width: `${scale}%` }" class="mx-auto">
        <VuePDF :pdf="pdf" :page="page" />
    </div>
</template>

<style>
canvas {
    width: 100% !important;
    height: auto !important;
}
</style>
