<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Preview from '@/Components/Forms/Preview.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import TextEditor from '@/Components/Forms/TextEditor.vue';
import Multiselect from '@/Components/Forms/MultiSelect.vue';
import RadioSwichButton from '@/Components/Forms/RadioSwichButton.vue';
import PDFViewer from '@/Components/Tools/PDFViewer.vue';

const props = defineProps({
  blog: {
    type: Object,
    required: false
  },
  bandsList: {
    type: Array,
    required: false
  }
});


const data = reactive({
  lang: props.blog?.title['am'] ? 'am' : 'en',
  author: false,
  cover: null,
  preview: props.blog?.images_url ? [...props.blog.images_url] : []
});

const langs = [
  { name: 'Eng', key: 'en' },
  { name: 'Arm', key: 'am' },
  { name: 'Rus', key: 'ru' }
];

const form = useForm(
  props.blog
    ? {
      ...props.blog,
      cover_file: null,
      pdf_file: null,
      _method: 'PUT'
    }
    : {
      cover_file: null,
      title: { en: '', am: '' },
      description: { en: '', am: '' },
      content: { en: '', am: '' },
      author: '',
      bands: [],
      pdf_file: null,
      pdf: null
    }
);

const createBlog = () => {
  form.post(
    route(
      form.id ? 'profile.blogs.update' : 'profile.blogs.store',
      form.id
    ),
    {
      onError: () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      },
      preserveScroll: true
    }
  );
};
</script>

<template>
  <ProfileLayout :meta="{title: 'Blog create'}">
    <form @submit.prevent="createBlog" class="flex flex-col gap-y-2">
      <div class="flex flex-col gap-4">
        <div class="flex md:flex-row flex-col w-full gap-2">
          <Preview
            label="cover"
            classes="bg-cover"
            class="min-h-96 md:w-1/2 w-full"
            labelClass="h-full"
            :image="form.cover?.large"
            v-model:preview="form.cover_file"
          />
          <div class="flex flex-col gap-2 md:w-1/2 w-full">
            <div class="flex gap-x-2">
              <input
                class="w-4/6"
                type="text"
                v-model="form.title[data.lang]"
                placeholder="Title"
              />
              <RadioSwichButton
                class="w-2/6"
                v-model:selectedOption="data.lang"
                :options="langs"
              />
            </div>
            <textarea
              type="text"
              rows="4"
              v-model="form.description[data.lang]"
              placeholder="Short description"
            />
            <Multiselect v-model="form.bands" :options="bandsList" text="Bands" multiple />

            <div v-if="form.author || data.author" class="flex flex-col gap-y-2">
              <div class="flex">
                <input
                  type="text"
                  v-model="form.author"
                  placeholder="Author"
                />
                <button type="button" class="bg-red px-4 bg-opacity-40 hover:bg-opacity-100"
                        @click="()=>{data.author = false; form.author=''}">x
                </button>
              </div>
            </div>
            <button v-if="!data.author" type="button" @click="()=>data.author = true"
                    class="bg-graydark2 rounded-sm w-fit p-2">Specify another author
            </button>

            <div class="text-gray">
              <div>
                <b>Note :</b>
                <p>The article can be in English, Armenian, or both</p>
                <p>If you are not the author of the article, please indicate the author by clicking
                  on the "Specify another author" button</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="relative mt-2">
        <TextEditor
          :key="data.lang"
          v-model:content="form.content[data.lang]"
          class="h-64"
          collection="event-image"
        />
      </div>
      <div class="flex items-center gap-x-2">
        <label for="pdf" class="text-gray">PDF</label>
        <input
          type="file"
          id="pdf"
          accept=".pdf"
          ref="pdfInput"
          @change="(e) => form.pdf_file = e.target.files[0]"
          class="hidden"
        />
        <button
          type="button"
          class="bg-graydark2 rounded-sm p-2 text-pretty hover:bg-opacity-100"
          @click="$refs.pdfInput.click()"
        >
          Select PDF
        </button>
      </div>
      <PDFViewer
        v-if="form.pdf_file ?? form.pdf"
        :key="form.pdf_file ? form.pdf_file.name + form.pdf_file.lastModified : form.pdf"
        :file="form.pdf_file ?? form.pdf"
      />
      <PrimaryButton
        class="ms-4"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{ form.id ? 'Update' : 'Create' }}
      </PrimaryButton>
    </form>
  </ProfileLayout>
</template>
