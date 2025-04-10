<script setup>
import ProgressBar from 'primevue/progressbar';
import { computed } from 'vue';
import SelectImages from '@/Components/Forms/SelectImages.vue';
import { useForm } from '@inertiajs/vue3';
import Multiselect from '@/Components/Forms/MultiSelect.vue';
import DatePicker from '@/Components/Forms/DatePicker.vue';
import AuthLayouth from '@/Layouts/AuthLayouth.vue';

defineProps({
    galleries: {
        type: String
    }
});

const form = useForm({
    title: '',
    description: '',
    images: [],
    preview: []
});

const limit = 150;

const percent = computed(() => {
    return (form.preview?.length / limit) * 100;
});

const submitGallery = () => {
    form.post(route('db.gallery.store', form?.value.id), {
        preserveState: false,
        preserveScroll: true
    });
};

const removeGallery = (id) => {
    form.delete(route('db.gallery.destroy', id), {
        preserveState: false,
        preserveScroll: true,
        onSuccess: () => {
            router.reload();
        }
    });
};

const removeImageQuery = (id) => {
    form.delete(route('db.media.destroy', id), {
        preserveState: false,
        preserveScroll: true
    });
};
</script>

<template>
    <AuthLayouth :meta="{title: 'Events'}">
        <ProgressBar class="w-full bg-green"
                     :class="percent > 70 ? 'warning' : '' "
                     :value="percent < 10 ? 5 : percent">
            {{ form.preview?.length }}/{{ limit }}
        </ProgressBar>

        <div>
            <div v-for="(gallery, index) in form.galleries" :key="index">
                <h2 class="text-center">{{ gallery.title }}</h2>
                <div class="flex flex-wrap gap-2 mt-5 ">
                    <div v-for="image in gallery.images" :key="image.id" class="w-24">
                        <img v-if="image.thumb && image.thumb.trim()"
                             :src="image.thumb"
                             class="object-cover"
                             alt="Image"
                             @error="$event.target.src = image.original" />
                        <img v-else-if="image.original"
                             :src="image.original"
                             class="object-cover"
                             alt="Image" />
                        <button type="button" @click="removeImageQuery(image.id)">Remove</button>
                    </div>
                </div>
                <button
                    type="button"
                    class="px-4 mt-10 py-2 bg-red-500 text-white rounded flex gap-x-2 bg-red"
                    @click="removeGallery(gallery.id)">
                    Remove Gallery
                    <!--                        <DeleteIcon />-->
                </button>
            </div>


            <div class="my-10">
                <div class="flex gap-x-4">
                    <div class="w-1/2">
                        <DatePicker
                            :flow="['calendar']"
                            v-model:start_date="form.start_date"
                            v-model:start_time="form.start_time"
                        />
                    </div>
                    <div class="w-1/2 flex flex-col gap-y-2">
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
                        <Multiselect text="Bands" multiple />
                        <Multiselect text="Location" />
                    </div>
                </div>
                <SelectImages
                    classes="mt-10"
                    v-model:previews="form.preview"
                    v-model:files="form.images" />
                <button v-if="form.images.length" @click="submitGallery"
                        class="px-4 mt-10 py-2 bg-dark-orange mx-auto text-white rounded flex gap-x-2">
                    Create gellery
                </button>
            </div>
        </div>
    </AuthLayouth>
</template>
