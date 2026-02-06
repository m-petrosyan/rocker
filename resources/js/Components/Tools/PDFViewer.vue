<script setup>
import { defineAsyncComponent, onMounted, ref } from 'vue';
import ZoomInIcon from '@/Components/Icons/ZoomInIcon.vue';
import ZoomOutIcon from '@/Components/Icons/ZoomOutIcon.vue';
import BookIcon from '@/Components/Icons/BookIcon.vue';
import FitPagesIcon from '@/Components/Icons/FitPagesIcon.vue';
import ArrowForward from '@/Components/Icons/ArrowForward.vue';
import ArrowBack from '@/Components/Icons/ArrowBack.vue';
import PagesIcon from '@/Components/Icons/PagesIcon.vue';

const VuePDF = defineAsyncComponent(() =>
  import('@tato30/vue-pdf').then(m => m.VuePDF)
);

const props = defineProps({
  file: { type: [Object, String], required: true }
});

const isMounted = ref(false);
const scale = ref(75);
const onePageMode = ref(false);
const page = ref(1);

const pdfData = ref({ pdf: null, pages: 0 });

onMounted(async () => {
  const { usePDF } = await import('@tato30/vue-pdf');

  const fileUrl = props.file instanceof File ? URL.createObjectURL(props.file) : props.file;
  const { pdf, pages } = usePDF(fileUrl);

  pdfData.value.pdf = pdf;
  pdfData.value.pages = pages;
  isMounted.value = true;
});
</script>

<template>
  <div v-if="isMounted">
    <div class="relative bg-graydark2 flex items-center justify-between p-2 rounded-t-lg mt-20 mx-auto"
         :style="{ width: `${scale}%` }">

      <div class="flex items-center gap-2 text-white">
        <p class="flex items-center gap-2">
          <PagesIcon />
          <span v-if="!onePageMode"> {{ pdfData.pages }} </span>
        </p>

        <div v-if="onePageMode" class="flex items-center gap-2">
          <button v-if="page !== 1" @click.prevent="page = page - 1">
            <ArrowBack />
          </button>
          <span>{{ page }} / {{ pdfData.pages }}</span>
          <button v-if="page !== pdfData.pages" @click.prevent="page = page + 1">
            <ArrowForward />
          </button>
        </div>
      </div>

      <div class="absolute left-1/2 -translate-x-1/2 flex items-center justify-center gap-2 text-white">
        <button @click.prevent="scale = scale > 50 ? scale - 25 : scale">
          <ZoomOutIcon />
        </button>
        <div class="mx-2">{{ scale }}%</div>
        <button @click.prevent="scale = scale < 100 ? scale + 25 : scale">
          <ZoomInIcon />
        </button>
      </div>

      <div>
        <button @click.prevent="onePageMode = !onePageMode" class="text-white">
          <BookIcon v-if="!onePageMode" />
          <FitPagesIcon v-else />
        </button>
      </div>
    </div>

    <div class="mx-auto" :style="{ width: `${scale}%` }">
      <div v-if="!onePageMode">
        <div v-for="p in pdfData.pages" :key="p" class="pb-1 page bg-white">
          <VuePDF :pdf="pdfData.pdf" :page="p" :scale="3" />
        </div>
      </div>
      <div v-else class="page bg-white">
        <VuePDF :pdf="pdfData.pdf" :page="page" :scale="3" />
      </div>
    </div>
  </div>

  <div v-else class="mt-20 text-center text-gray-400">
    Загрузка PDF...
  </div>
</template>

<style scoped lang="scss">
:deep(.page) {
  box-shadow: 4px 4px 8px 0px rgba(0, 0, 0, 0.3);
}

:deep(canvas) {
  width: 100% !important;
  height: auto !important;
  display: block;
}
</style>