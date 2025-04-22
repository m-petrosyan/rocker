<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DatePicker from '@/Components/Forms/DatePicker.vue';
import Preview from '@/Components/Forms/Preview.vue';
import GoogleAutocomplate from '@/Components/Maps/GoogleAutocomplate.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import Multiselect from '@/Components/Forms/MultiSelect.vue';

const props = defineProps({
    band: {
        type: Object,
        required: false
    },
    bandsList: {
        type: Array,
        required: false
    }
});


const data = reactive({
    options: {
        price: +!!props.event?.price || +!!props.event?.ticket,
        link: +!!props.event?.link
    },
    preview: null,
    created: false,
    disable: false
});

const form = useForm(
    props.event
        ? { ...props.band }
        : {
            name: '',
            genre: '',
            info: ''
        }
);

const createBand = () => {
    form.post(
        route(
            props.band?.id ? 'profile.bands.update' : 'profile.bands.store',
            props.band?.id
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
    <ProfileLayout :meta="{title: 'Band create'}">
        <div>
            <form @submit.prevent="createBand" class="flex flex-col gap-y-2">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <Preview
                            class="h-full min-h-96"
                            label="h-full"
                            :image="form.cover"
                            v-model:preview="form.cover_file"
                            v-model:file="data.cover"
                        />
                        <Preview
                            class="h-full min-h-96"
                            label="h-full"
                            :image="form.logo"
                            v-model:preview="form.logo_file"
                            v-model:file="data.logo"
                        />
                    </div>
                    <div class="flex w-full md:w-1/2 flex-col gap-y-2">
                        <div>
                            <input
                                class="w-full bg-graydark2"
                                type="text"
                                v-model="form.name"
                                placeholder="Name"
                                tabindex="1"
                                enterkeyhint="next"
                            />
                        </div>
                        <div>
                            <input
                                class="w-full bg-graydark2"
                                type="text"
                                v-model="form.genre"
                                placeholder="Genres"
                                tabindex="1"
                                enterkeyhint="next"
                            />
                        </div>
                        <GoogleAutocomplate :form="form" />
                        <Multiselect v-model="form.bands" :options="bandsList" text="Bands" multiple />
                        <DatePicker
                            v-model:start_date="form.start_date"
                            v-model:start_time="form.start_time"
                        />
                    </div>
                </div>
                <div class="relative mt-2">
                    <textarea
                        class="w-full bg-graydark2"
                        type="text"
                        rows="10"
                        v-model="form.info"
                        placeholder="Description"
                        tabindex="2"
                        enterkeyhint="next"
                    >
                    </textarea>
                </div>

                <br />
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Create
                </PrimaryButton>
            </form>
        </div>
    </ProfileLayout>
</template>
