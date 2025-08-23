<script setup>
import ImageUploader from 'quill-image-uploader';
import { QuillEditor } from '@vueup/vue-quill';

const props = defineProps({
    collection: {
        type: String,
        default: ''
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

const customToolbar = [
    ['bold', 'italic'],
    [{ 'list': 'bullet' }],
    [{ 'indent': '-1' }, { 'indent': '+1' }],
    [{ 'align': [] }],
    // ['image'],
    ['link'],
    ['video'],
    ['clean']
];

</script>

<template>
    <QuillEditor
        class="quill-with-upload"
        :modules="modules"
        contentType="html"
        :toolbar="customToolbar"
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

.ql-toolbar.ql-snow .ql-video {
    background-image: url('/images/icon/youtube.png') !important;
    background-size: 80% !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    width: 24px !important;
    height: 24px !important;
    filter: grayscale(1) brightness(1.5);

    svg {
        display: none !important;
    }

    &:hover {
        background-image: url('/images/icon/youtube.png') !important;
        filter: grayscale(0);
    }

}

.ql-snow .ql-tooltip {
    background-color: theme('colors.graydark2');
    border: none;
    box-shadow: none;

    &[data-mode=video]::before {
        content: "Youtube url:" !important;
    }

    input {
        background-color: white !important;

        &::placeholder {

        }
    }

    .ql-action {
        color: white !important;
    }

}


.ql-toolbar.ql-snow .ql-video::before {
    content: '' !important;
}
</style>
