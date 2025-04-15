<script setup>
import QrGenerate from '@/Components/Profile/QrGenerate.vue';
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    url: {
        type: String,
        required: true
    },
    owner: {
        type: Boolean,
        required: false
    }
});

const form = useForm({
    image: null
});

const submit = () => {
    form.post(route('profile.image.update'), {
        preserveScroll: false
    });
};

const previewFile = ref();

const changePreview = (file) => {
    previewFile.value = URL.createObjectURL(file.target.files[0]);
    form.image = file.target.files[0];
    submit();
};

const previewStyle = computed(() => {
    const imageUrl = previewFile.value ?? props.user?.image?.thumb;

    return imageUrl
        ? { backgroundImage: `url(${imageUrl})` }
        : { backgroundImage: `url(/images/user.jpg)` };
});

</script>

<template>
    <div class="absolute left-1/2 top-[-80px] w-fit -translate-x-1/2 text-center">
        <div class="relative mx-auto w-fit">
            <div v-if="owner">
                <input type="file" hidden accept="image/*" id="preview"
                       @change="changePreview">
                <label
                    class="cursor-pointer block mt-6 h-32 w-32 rounded-full border-4 border-white bg-cover bg-center overflow-hidden"
                    for="preview"
                    :style="previewStyle">
                    <span v-if="!user.image && !previewFile"
                          class="flex justify-center items-center bg-blackTransparent h-full w-full">Click to upload</span>
                </label>
            </div>
            <img v-else
                 :src="props.user?.image?.thumb ?? '/images/user.jpg'"
                 alt="Profile Picture"
                 class="mx-auto mt-6 h-32 w-32 rounded-full border-4 border-white object-cover"
            />

            <QrGenerate v-if="url" :url />
        </div>
        <h3 class="text-gray-900 p-6">
            {{ user.name }}
        </h3>
    </div>
</template>
