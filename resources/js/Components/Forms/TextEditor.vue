<script setup>
import ImageUploader from 'quill-image-uploader';
import { QuillEditor } from '@vueup/vue-quill';

const props = defineProps({
    collection: {
        type: String,
        required: true
    }
});

axios.defaults.withCredentials = true;
const token = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

const modules = {
    name: 'imageUploader',
    module: ImageUploader,
    options: {
        upload: file => {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('collection', props.collection);
                formData.append('image', file);

                axios.post('/profile/media', formData)
                    .then(res => resolve(res.data.url))
                    .catch(error => {
                        reject('Upload failed');
                    });
            });
        }
    }
};

</script>

<template>
    <QuillEditor
        class="quill-with-upload"
        :modules
        contentType="html"
        toolbar="full"
    />
</template>

<style>

.ql-container.ql-snow {
    border: none;
}

.ql-toolbar.ql-snow {
    border: 1px dotted theme('colors.graydark2');
}

.quill-with-upload {
    .ql-editor {
        color: white;
        background-color: theme('colors.graydark2');
        min-height: 300px;

        .ql-video {
            width: 100%;
            height: 500px;
        }
        
        .ql-align-center img {
            margin: 0 auto !important;
        }

        .ql-align-right img {
            float: right !important;
        }
    }
}
</style>
