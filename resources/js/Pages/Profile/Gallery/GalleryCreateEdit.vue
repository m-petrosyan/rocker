<script setup>
import ProgressBar from 'primevue/progressbar';
import { computed, onBeforeMount, onBeforeUnmount, reactive, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import SelectImages from '@/Components/Forms/SelectImages.vue';
import Multiselect from '@/Components/Forms/MultiSelect.vue';
import DatePicker from '@/Components/Forms/DatePicker.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import GoogleAutocomplate from '@/Components/Maps/GoogleAutocomplate.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    gallery: {
        type: Object,
        required: false
    },
    bandsList: []
});

const form = useForm(
    props.gallery?.id
        ? {
            ...props.gallery,
            images: null,
            cid: null,
            location: null,
            cordinates: null,
            cover: null,
            _method: 'PUT'
        }
        : {
            title: '',
            description: '',
            date: null,
            images: [],
            bands: [],
            cid: null,
            location: null,
            cordinates: null,
            cover: null
        }
);

onBeforeMount(() => {
    if (props.gallery?.venue) {
        const { id, ...rest } = props.gallery.venue;
        Object.assign(form, rest);
    }
});

const data = reactive({
    preview: props.gallery?.images_url ? [...props.gallery.images_url] : []
});

const limit = 200;

const percent = computed(() => {
    return (data.preview?.length / limit) * 100;
});

// Функция для обработки события beforeunload (для закрытия  const handleBeforeUnload = (event) => {
if (form.processing) {
    event.preventDefault();
    event.returnValue = ''; // Показывает стандартное предупреждение браузера
}
};

// Функция для обработки навигации Inertia
const handleInertiaBefore = (event) => {
    if (form.processing) {
        if (!confirm('Форма в процессе загрузки. Вы уверены, что хотите покинуть страницу?')) {
            event.preventDefault(); // Отменяем навигацию
        }
    }
};

// Следим за form.processing
watch(() => form.processing, (isProcessing) => {
    if (isProcessing) {
        window.addEventListener('beforeunload', handleBeforeUnload);
        router.on('before', handleInertiaBefore);
    } else {
        window.removeEventListener('beforeunload', handleBeforeUnload);
        router.off('before', handleInertiaBefore);
    }
});

// Очищаем обработчики при уничтожении компонента
onBeforeUnmount(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    router.off('before', handleInertiaBefore);
});

const submitGallery = () => {
    form.post(route(form.id ? 'profile.galleries.update' : 'profile.galleries.store', form.id), {
        preserveScroll: false
    });
};
</script>

<template>
    <ProfileLayout :meta="{title: 'Gallery create'}">
        <div>
            <form @submit.prevent="submitGallery" class="px-4 md:px-0">
                <div class="flex flex-col-reverse md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <DatePicker
                            :flow="['calendar']"
                            v-model:start_date="form.date"
                        />
                    </div>
                    <div class="w-full md:w-1/2 flex flex-col gap-y-2">
                        <input
                            class="bg-graydark2 w-full mx U-auto block"
                            type="text"
                            v-model="form.title"
                            placeholder="Title"
                            tabindex="1"
                            enterkeyhint="next"
                        />
                        <textarea
                            class="bg-graydark2 w-full mx-auto block"
                            type="text"
                            v-model="form.description"
                            placeholder="Description"
                            tabindex="1"
                            enterkeyhint="next"
                        />
                        <Multiselect v-model="form.bands" :options="bandsList" text="Bands" multiple />
                        <GoogleAutocomplate :form="form" />
                    </div>
                </div>

                <ProgressBar
                    v-show="data preview?.length"
                    class="w-full bg-green mt-10"
                    :class="percent > 70 ? 'warning' : ''"
                    :value="percent < 10 ? 5 : percent"
                >
                    {{ data.preview?.length }}/{{ limit }}
                </ProgressBar>

                <SelectImages
                    v-model:cover="form.cover"
                    v-model:previews="data.preview"
                    v-model:files="form.images"
                />
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.id ? 'Update' : 'Create' }}
                </PrimaryButton>
            </form>
        </div>
    </ProfileLayout>
</template>
