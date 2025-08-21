<script setup>
import { reactive } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Preview from '@/Components/Forms/Preview.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import Multiselect from '@/Components/Forms/MultiSelect.vue';
import TextEditor from '@/Components/Forms/TextEditor.vue';
import SelectImages from '@/Components/Forms/SelectImages.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';
import AddLinks from '@/Components/Forms/AddLinks.vue';

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
    disable: false,
    preview: props.band?.images_url ? [...props.band.images_url] : []
});

const form = useForm(
    props.band
        ? {
            ...props.band,
            cover_file: null,
            logo_file: null,
            _method: 'PUT',
            images: []
        }
        : {
            cover_file: null,
            logo_file: null,
            images: [],
            name: '',
            genres: '',
            info: '',
            links: [],
            albums: [],
            cover_position: { x: 50, y: 50 }
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

const confirmDelete = (album) => {
    if (confirm('Are you sure you want to delete this album?')) {
        if (album.id) {
            router.delete(route('profile.album.delete', album.id), {
                onError: () => {
                    alert('Error deleting album');
                }
            });
        }
        form.albums.splice(form.albums.indexOf(album), 1);
    }
};

const addAlbum = () => {
    form.albums.push({
        title: '',
        cover_file: null,
        tracks_count: null,
        year: '',
        links: [{
            url: ''
        }]
    });
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
                            v-model:backgroundPosition="form.cover_position"
                            v-model:preview="form.cover_file"
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
                        />
                        <div class="flex flex-col gap-2 md:w-1/2 w-full">
                            <Multiselect
                                v-tooltip="'If the group name is already in the list, please select from the list'"
                                v-model="form.name" :options="bandsList" text="Name"
                                :disabled="band" />
                            <Multiselect
                                v-tooltip="'You can choose from the list or add if there is no list'"
                                v-model="form.genres" :options="genres" text="Genres" multiple />
                            <AddLinks maxLinks="3" v-model:data="form.links" tooltip="Add your social networks" />
                            <div class="text-gray">
                                <b>Note :</b>
                                <p class="text-orange">A mandatory requirement for adding a band is that it must have
                                    performed
                                    concerts.</p>
                                <div class="flex">
                                    <p>You can also add a video from youtube by clicking the button</p>
                                    <img src="/images/video_instruction.png" alt="instruction">
                                </div>
                                <p>Tell us a bit about your band!
                                    When did you start? Who's in the lineup? Just a few words about your story, vibe,
                                    and members will make your profile way cooler.
                                </p>
                                <p>For all questions write <a href="https://t.me/mpetrosyan1" target="_blank">@mpetrosyan1</a>
                                </p>
                                <SuccessMessages success class="mt-4"
                                                 :message="'You can add us to the guest list,  we’ll arrange for a photographer to attend and capture the concert 📸'" />
                            </div>

                        </div>
                    </div>
                </div>
                <SelectImages
                    limit="6"
                    label="Click or drag files here (up to 6 images)"
                    v-model:previews="data.preview"
                    v-model:files="form.images"
                />
                <div class="relative mt-2"
                     v-tooltip="'Please fill in the group information \n You can also add a video from youtube by clicking the video button'">
                    <TextEditor
                        v-model:content.trim="form.info"
                        class="h-64"
                        collection="event-image"
                        :error="form.errors.info"
                    />
                </div>
                <h3 class="text-center text-gray mt-4">Albums</h3>
                <div class="mt-2 grid-cols-3 grid gap-4">
                    <div v-for="(album, index) in form.albums" :key="`album-${album.id}`">
                        <Preview
                            :label="`album-cover-${album.id}-${index}`"
                            classes="bg-cover"
                            class="min-h-96 w-full"
                            labelClass="h-full"
                            :image="album.cover"
                            v-model:preview="album.cover_file"
                        />
                        <input
                            type="text"
                            class="w-full mt-1"
                            v-model="album.title"
                            placeholder="Title"
                        />
                        <div class="flex mt-2 gap-2">
                            <input
                                type="number"
                                class="w-full"
                                v-model="album.tracks_count"
                                placeholder="Tracks count"
                                min="1"
                                max="100"
                            />
                            <select
                                v-model="album.year"
                                class="w-3/6 mt-2"

                            >h
                                <option disabled value="">Select year</option>
                                <option
                                    v-for="year in Array.from({length: new Date().getFullYear() - 1979}, (_, i) => new Date().getFullYear() - i)"
                                    :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                        <div class="flex flex-col mt-2 gap-2">
                            <AddLinks maxLinks="3" v-model:data="album.links" tooltip="Add album url" />
                        </div>
                        <button
                            type="button"
                            class="bg-red w-full mt-2 flex justify-center items-center py-1 bg-opacity-40 hover:bg-opacity-100"
                            @click="confirmDelete(album)"
                        >
                            Delete album
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="addAlbum"
                        class="flex min-h-64 items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2"
                    >
                        <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
                            <h2 class="text-3xl">+</h2>
                        </div>
                    </button>

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
