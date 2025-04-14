<script setup>
import ProgressBar from 'primevue/progressbar';
import { computed, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
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
        ? { ...props.gallery, images: null, _method: 'PUT' }
        : {
            title: '',
            description: '',
            date: null,
            images: [],
            bands: []
        });

const data = reactive({
    preview: props.gallery?.images_url ? [...props.gallery.images_url] : []
});

const limit = 200;

const percent = computed(() => {
    return (data.preview?.length / limit) * 100;
});

const submitGallery = () => {
    form.post(route(form.id ? 'profile.gallery.update' : 'profile.gallery.store', form.id), {
        preserveScroll: false
    });
};

const removeGallery = (id) => {
    form.delete(route('profile.gallery.destroy', id), {
        preserveState: false,
        preserveScroll: true,
        onSuccess: () => {
            router.reload();
        }
    });
};

const removeImageQuery = (id) => {
    form.delete(route('profile.media.destroy', id), {
        preserveState: false,
        preserveScroll: true
    });
};
</script>

<template>
    <ProfileLayout :meta="{title: 'Gallery create'}">
        <div>

            <!--            <div v-for="image in gallery.images" :key="image.id" class="w-24">-->
            <!--                <img v-if="image.thumb && image.thumb.trim()"-->
            <!--                     :src="image.thumb"-->
            <!--                     class="object-cover"-->
            <!--                     alt="Image"-->
            <!--                     @error="$event.target.src = image.original" />-->
            <!--                <img v-else-if="image.original"-->
            <!--                     :src="image.original"-->
            <!--                     class="object-cover"-->
            <!--                     alt="Image" />-->
            <!--                <button type="button" @click="removeImageQuery(image.id)">Remove</button>-->
            <!--            </div>-->
            <!--       -->

            <form @submit.prevent="submitGallery" class="px-4 md:px-0">
                <div class="flex flex-col-reverse md:flex-row flex-rverse gap-4">
                    <div class="w-full md:w-1/2">
                        <DatePicker
                            :flow="['calendar']"
                            v-model:start_date="form.date"
                        />
                    </div>
                    <div class="w-full md:w-1/2 flex flex-col gap-y-2">
                        <input
                            class="bg-graydark2 w-full mx-auto block"
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

                <ProgressBar v-show="data.preview?.length" class="w-full bg-green mt-10"
                             :class="percent > 70 ? 'warning' : '' "
                             :value="percent < 10 ? 5 : percent">
                    {{ data.preview?.length }}/{{ limit }}
                </ProgressBar>


                <SelectImages
                    v-model:previews="data.preview"
                    v-model:files="form.images" />
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Create gellery
                </PrimaryButton>
            </form>
        </div>
    </ProfileLayout>
</template>
