<script setup>
import { ref } from 'vue';

const props = defineProps({
  spotifyId: {
    type: String,
    default: null
  }
});

const embedUrl = ref(`https://open.spotify.com/embed/album/${props.spotifyId}?si=XRnW-3deR0mQrq_UmbKAtQ&nd=1&dlsi=ba36746cb66e4450`);
</script>


<template>
  <div class="h-full w-full">
    <button
      @click="$emit('close')"
      class="absolute top-3 sm:top-4 right-[3%] bg-black bg-black text-white py-1 px-2 rounded-full hover:bg-opacity-80 transition-all z-20"
    >
      ✕
    </button>
    <iframe
      :src="embedUrl"
      class="w-full h-full rounded-lg"
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
