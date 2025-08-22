<script setup>
import { router } from '@inertiajs/vue3';
import Preview from '@/Components/Forms/Preview.vue';
import AddLinks from '@/Components/Forms/AddLinks.vue';
import MusicIcon from '@/Components/Icons/MusicIcon.vue';
import DateIcon from '@/Components/Icons/DateIcon.vue';
import { getHostname } from '@/Helpers/urlHelper.js';

const props = defineProps({
    album: { type: Object, required: true },
    index: { type: Number, required: true },
    canEdit: { type: Boolean, default: false }
});

const emit = defineEmits(['delete']);

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this album?')) {
        if (props.album.id) {
            router.delete(route('profile.album.delete', props.album.id), {
                onSuccess: () => {
                    emit('delete', props.album);
                },
                onError: () => {
                    alert('Error deleting album');
                }
            });
        } else {
            emit('delete', props.album);
        }
    }
};
</script>

<template>
    <div v-if="canEdit">
        <Preview
            :label="`album-cover-${album.id || 'new'}-${index}`"
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
            <select v-model="album.year" class="w-3/6 mt-2">
                <option disabled value="">Select year</option>
                <option
                    v-for="year in Array.from({ length: new Date().getFullYear() - 1979 }, (_, i) => new Date().getFullYear() - i)"
                    :key="year"
                    :value="year"
                >
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
            @click="confirmDelete"
        >
            Delete album
        </button>
    </div>
    <div v-else>
        <div class="relative group aspect-square">
            <div v-if="album.links.length"
                 class="opacity-0 group-hover:opacity-100 transition-opacity absolute top-0 flex flex-col gap-y-4 justify-center items-center h-full w-full bg-blackTransparent2">
                <a v-for="link in album.links" :key="link.id" :href="link.url"
                   target="_blank">{{ getHostname(link.url)
                    }}</a>
            </div>

            <div
                class="p-2 bg-cover h-full h-full flex justify-between items-end rounded-md overflow-hidden"
                :style="album.cover?.large ? `background-image: url('${album.cover?.large}')` : ''"
            >

                <div class="flex gap-2">
                    <MusicIcon />
                    {{ album.tracks_count }}
                </div>
                <div class="flex gap-2">
                    <DateIcon />
                    {{ album.year }}
                </div>
            </div>
        </div>
        <h3 class="text-center mt-1">{{ album.title }}</h3>
    </div>
</template>
