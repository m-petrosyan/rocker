<script setup>
import { ref } from 'vue';

const props = defineProps({
    spotifyId: {
        type: String,
        required: true
    }
});

const embedUrl = ref(`https://open.spotify.com/embed/album/${props.spotifyId}?si=XRnW-3deR0mQrq_UmbKAtQ&nd=1&dlsi=ba36746cb66e4450`);
</script>


<template>
    <div class="p-4">
        <button
            @click="$emit('close')"
            class="absolute top-6 right-3 bg-black bg-black text-white py-1 px-2 rounded-full hover:bg-opacity-80 transition-all z-20"
        >
            ✕
        </button>
        <h1 class="text-xl font-bold mb-4">🎵 Spotify Player</h1>
        <iframe
            :src="embedUrl"
            width="400"
            height="400"
            frameborder="0"
            allowtransparency="true"
            allow="encrypted-media"
        ></iframe>

        <!--        <div class="flex gap-2 mt-4">-->
        <!--            <button @click="playAlbum(testUrl)" class="px-4 py-2 bg-blue-600 text-white rounded">-->
        <!--                ▶️ Load Album-->
        <!--            </button>-->
        <!--        </div>-->
    </div>
</template>
