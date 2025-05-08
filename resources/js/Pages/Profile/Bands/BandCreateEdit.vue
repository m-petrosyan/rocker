<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Preview from '@/Components/Forms/Preview.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import Multiselect from '@/Components/Forms/MultiSelect.vue';
import TextEditor from '@/Components/Forms/TextEditor.vue';

const props = defineProps({
    band: {
        type: Object,
        required: false
    },
    genres: {
        type: Array,
        required: false
    },
    bandsList: {
        type: Array,
        required: false
    }
});


const data = reactive({
    cover: null,
    logo: null,
    disable: false
});

const form = useForm(
    props.band
        ? {
            ...props.band,
            cover_file: null,
            logo_file: null,
            _method: 'PUT'
        }
        : {
            cover_file: null,
            logo_file: null,
            name: '',
            genres: '',
            info: '',
            links: []
        }
);

const createBand = () => {
    form.post(
        route(
            form.id ? 'profile.bands.update' : 'profile.bands.store',
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

const addLinks = () => {
    form.links.push({
        url: ''
    });
};

const delLink = (index) => {
    form.links.splice(index, 1);
};
</script>

<template>
    <ProfileLayout :meta="{title: 'Band create'}">
        <div>
            <form @submit.prevent="createBand" class="flex flex-col gap-y-2">
                <div class="flex flex-col gap-4">
                    <div class="w-full">
                        <Preview
                            label="cover (1280px x 384px)"
                            class="min-h-96"
                            classes="bg-cover"
                            labelClass="h-full"
                            :image="form.cover"
                            v-model:preview="form.cover_file"
                            v-model:file="data.cover"
                        />
                    </div>
                    <div class="flex md:flex-row flex-col w-full gap-2">
                        <Preview
                            label="logo (300px x 300px Square)"
                            classes="bg-contain"
                            class="min-h-96 md:w-1/2 w-full"
                            labelClass="h-full"
                            :image="form.logo"
                            v-model:preview="form.logo_file"
                            v-model:file="data.logo"
                        />
                        <div class="flex flex-col gap-2 md:w-1/2 w-full">
                            <Multiselect v-model="form.name" :options="bandsList" text="Name" :disabled="band" />
                            <Multiselect v-model="form.genres" :options="genres" text="Genres" multiple />
                            <div v-if="form.links.length" class="flex flex-col gap-y-2">
                                <div v-for="(url, index) in form.links" class="flex">
                                    <input
                                        class="w-full bg-graydark2"
                                        type="text"
                                        v-model="form.links[index].url"
                                        placeholder="Url"
                                    />
                                    <button type="button" class="bg-red px-4 bg-opacity-40 hover:bg-opacity-100"
                                            @click="delLink(index)">x
                                    </button>
                                </div>
                            </div>
                            <button :disabled="form.links.length > 2" type="button" @click="addLinks"
                                    class="bg-grayblue w-fit p-2">Add url
                            </button>
                        </div>
                    </div>
                </div>
                <div class="relative mt-2">
                    <TextEditor
                        v-model:content="form.info"
                        class="h-64"
                        collection="event-image"
                        :error="form.errors.info"
                    />
                </div>

                <br />
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
