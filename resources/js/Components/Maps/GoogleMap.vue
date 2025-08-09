<template>
    <GoogleMap
        v-if="center"
        api-key="`import.meta.env.VITE_GOOGLE_MAPS_API_KEY`"
        :center="center"
        :zoom="18"
        :styles="darkTheme"
        class="w-full h-[500px]"
    >
        <Marker :options="{ position: center }" />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';
import { computed } from 'vue';


const props = defineProps({
    cordinates: {
        type: Object,
        required: true
    }
});

const center = computed(() => {
    return {
        lat: +props.cordinates?.latitude,
        lng: +props.cordinates?.longitude
    };
});

const darkTheme = [
    { elementType: 'geometry', stylers: [{ color: '#212121' }] },
    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#212121' }] },
    { featureType: 'administrative', elementType: 'geometry', stylers: [{ color: '#757575' }] },
    { featureType: 'road', elementType: 'geometry.fill', stylers: [{ color: '#2c2c2c' }] },
    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#000000' }] },
    { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#1a1a1a' }] }
];
</script>
