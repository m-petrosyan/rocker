<script setup>
import { usePDF, VuePDF } from '@tato30/vue-pdf';
import { ref } from 'vue';
import ZoomInIcon from '@/Components/Icons/ZoomInIcon.vue';
import ZoomOutIcon from '@/Components/Icons/ZoomOutIcon.vue';
import BookIcon from '@/Components/Icons/BookIcon.vue';
import FitPagesIcon from '@/Components/Icons/FitPagesIcon.vue';
import ArrowForward from '@/Components/Icons/ArrowForward.vue';
import ArrowBack from '@/Components/Icons/ArrowBack.vue';
import PagesIcon from '@/Components/Icons/PagesIcon.vue';

const props = defineProps({
    file: {
        type: [Object, String],
        required: true
    }
});


const fileUrl = props.file instanceof File ? URL.createObjectURL(props.file) : props.file;
const { pdf, pages } = usePDF(fileUrl);

const scale = ref(75);
const onePageMode = ref(false);
const page = ref(1);
</script>

<template>
    <div class="relative bg-graydark2 flex items-center justify-between p-2 rounded-t-lg mt-20 mx-auto"
         :style="{ width: `${scale}%` }">
        <div class="flex items-center gap-2">
            <p class="flex items-center gap-2">
                <PagesIcon />
                <span v-if="!onePageMode"> {{ pages }} </span></p>

            <div v-if="onePageMode" class="flex items-center gap-2">
                <button v-if="page !== 1" @click.prevent="page = page -1 ">
                    <ArrowBack />
                </button>
                <span v-if="onePageMode">{{ page }} / </span>
                <span> {{ pages }} </span>
                <button v-if="page !== pages" @click.prevent="page = page +1 ">
                    <ArrowForward />
                </button>
            </div>
        </div>
        <div class="absolute left-1/2 -translate-x-1/2 flex items-center justify-center gap-2">
            <button @click.prevent="scale = scale > 50 ? scale - 25 : scale">
                <ZoomOutIcon />
            </button>
            <div class="mx-2">{{ scale }}%</div>
            <button @click.prevent="scale = scale < 100 ? scale + 25 : scale">
                <ZoomInIcon />
            </button>
        </div>
        <div>
            <button v-if="!onePageMode" @click.prevent="onePageMode = true">
                <BookIcon />
            </button>
            <button v-else @click.prevent="onePageMode = false">
                <FitPagesIcon />
            </button>
        </div>
    </div>
    <div class="mx-auto" :style="{ width: `${scale}%` }">
        <div v-if="!onePageMode" v-for="page in pages" :key="page" class="pb-1 page bg-white">
            <VuePDF :pdf="pdf" :page="page" />
        </div>
        <VuePDF v-else :pdf="pdf" :page="page" />
    </div>
</template>

<style scoped lang="scss">
:deep(.page) {
    -webkit-box-shadow: 4px 4px 8px 0px rgba(84, 84, 84, 1.2);
    -moz-box-shadow: 4px 4px 8px 0px rgba(84, 84, 84, 1.2);
    box-shadow: 4px 4px 8px 0px rgba(84, 84, 84, 1.2);

}

:deep(canvas) {
    width: 100% !important;
    height: auto !important;
}
</style>
