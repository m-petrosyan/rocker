<script setup>
import { router } from '@inertiajs/vue3';
import Preview from '@/Components/Forms/Preview.vue';
import AddLinks from '@/Components/Forms/AddLinks.vue';
import MusicIcon from '@/Components/Icons/MusicIcon.vue';
import DateIcon from '@/Components/Icons/DateIcon.vue';
import { getHostname } from '@/Helpers/urlHelper.js';
import SpotifyPlayer from '@/Components/Tools/SpotifyPlayer.vue';
import { reactive } from 'vue';
import PlayIcon from '@/Components/Icons/PlayIcon.vue';

const props = defineProps({
    albums: { type: Array, required: true },
    canEdit: { type: Boolean, default: false }
});

const emit = defineEmits(['delete', 'update:albums']);

const playStates = reactive({});
props.albums.forEach(album => {
    const albumId = album.id || `temp-${Math.random()}`;
    playStates[albumId] = false;
});

const togglePlay = (album) => {
    const albumId = album.id || `temp-${Math.random()}`;
    Object.keys(playStates).forEach(id => {
        if (id !== albumId) playStates[id] = false;
    });
    playStates[albumId] = !playStates[albumId];
};

const closePlayer = (album) => {
    const albumId = album.id || `temp-${Math.random()}`;
    playStates[albumId] = false;
};

const getPlayState = (album) => {
    const albumId = album.id || `temp-${Math.random()}`;
    return playStates[albumId] || false;
};

const confirmDelete = (album) => {
    if (confirm('Are you sure you want to delete this album?')) {
        if (album.id) {
            router.delete(route('profile.album.delete', album.id), {
                onSuccess: () => {
                    emit('delete', album);
                },
                onError: () => {
                    alert('Error deleting album');
                }
            });
        } else {
            deleteAlbum(album);
        }
    }
};

const addAlbum = () => {
    const newAlbums = [...props.albums, {
        title: '',
        cover_file: null,
        tracks_count: null,
        year: '',
        links: [{ url: '' }]
    }];
    emit('update:albums', newAlbums);
};

const deleteAlbum = (album) => {
    const newAlbums = props.albums.filter(a => a !== album);
    emit('update:albums', newAlbums);
};

// const updateAlbum = (index, key, value) => {
//     const newAlbums = [...props.albums];
//     newAlbums[index] = { ...newAlbums[index], [key]: value };
//     emit('update:albums', newAlbums);
// };

const getSpotifyId = (album) => {
    if (!album.links?.length) return null;
    const spotifyLink = album.links.find(link =>
        link.url?.includes('open.spotify.com/album/')
    );
    if (!spotifyLink) return null;

    // извлекаем хэш (ID альбома)
    const match = spotifyLink.url.match(/album\/([a-zA-Z0-9]+)/);
    return match ? match[1] : null;
};
</script>


<template>
    <div class="mt-20">
        <h3 v-if="albums.length" class="text-center">Albums</h3>
        <div class="mt-2 lg:grid-cols-3 md:grid-cols-2 grid-cols-1 grid gap-4 mt-4">
            <div v-for="(album, index) in albums" :key="album.id || index">
                <div v-if="canEdit">
                    <Preview
                        :label="`album-cover-${album.id || 'new'}-${index}`"
                        classes="bg-cover"
                        class="min-h-96 w-full"
                        labelClass="h-full"
                        :image="album.cover?.thumb"
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
                    <p class="text-gray p-2">If your album is available on Spotify, add the link so users can play it
                        directly here."</p>

                    <button
                        type="button"
                        class="bg-red w-full mt-2 flex justify-center items-center py-1 bg-opacity-40 hover:bg-opacity-100"
                        @click="confirmDelete(album)"
                    >
                        Delete album
                    </button>
                </div>
                <div v-else>
                    <div class="perspective-1000 mb-2">
                        <div
                            class="relative group aspect-square transition-all duration-700 ease-in-out preserve-3d"
                            :class="{ 'rotate-y-180': getPlayState(album) }"
                        >
                            <div class="absolute inset-0 w-full h-full backface-hidden">
                                <div v-if="album.links.length"
                                     v-show="!getPlayState(album)"
                                     class="opacity-0 group-hover:opacity-100 transition-opacity absolute top-0 flex flex-col gap-y-4 justify-center items-center h-full w-full bg-blackTransparent2 rounded-md z-10">
                                    <a v-for="link in album.links" :key="link.id" :href="link.url"
                                       target="_blank"
                                       class="text-white hover:text-red transition-colors px-4 py-2 rounded-md">
                                        {{ getHostname(link.url) }}
                                    </a>
                                </div>

                                <div
                                    class="bg-cover h-full flex items-end rounded-md overflow-hidden relative"
                                    :style="album.cover?.thumb ? `background-image: url('${album.cover?.thumb}')` : `background-image: url('${'/images/vinyl.jpg'}')` "
                                >
                                    <button
                                        v-if="getSpotifyId(album)"
                                        @click="togglePlay(album)"
                                        class="absolute top-2 right-2 bg-blackTransparent2 p-1 rounded-full hover:bg-opacity-80 transition-all z-20">
                                        <PlayIcon />
                                    </button>
                                    <div class="w-full flex justify-between items-end">
                                        <div class="flex gap-2 bg-blackTransparent2 py-1 px-2 rounded-sm">
                                            <MusicIcon />
                                            <p> {{ album.tracks_count }}</p>
                                        </div>
                                        <div class="flex gap-2 bg-blackTransparent2 py-1 px-2 rounded-sm">
                                            <DateIcon />
                                            <p>{{ album.year }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180">
                                <SpotifyPlayer
                                    :spotifyId="getSpotifyId(album)"
                                    @close="closePlayer(album)"
                                />
                            </div>
                        </div>
                    </div>

                    <h3 class="text-center">{{ album.title }}</h3>
                </div>
            </div>
            <button
                v-if="canEdit"
                type="button"
                @click="addAlbum"
                class="flex min-h-64 items-center gap-2 border-2 border-dashed border-graydark2 p-4
            hover:border-orange hover:bg-graydark2"
            >
                <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
                    <h2 class="text-3xl">+</h2>
                </div>
            </button>
        </div>
    </div>
</template>

<style scoped>
.rotate-y-180 {
    transform: rotateY(180deg);
}

.backface-hidden {
    backface-visibility: hidden;
}

.preserve-3d {
    transform-style: preserve-3d;
}

.perspective-1000 {
    perspective: 1000px;
}
</style>
