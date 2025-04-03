<template>
    <GoogleMap
        v-if="center"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="center"
        :zoom="18"
        :styles="darkTheme"
        :map-options="mapOptions"
        style="width: 100%; height: 500px"
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
    { elementType: 'geometry', stylers: [{ color: '#212121' }] }, // Background
    { elementType: 'labels.icon', stylers: [{ visibility: 'on' }] }, // Icons visible
    { elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] }, // Text color
    { elementType: 'labels.text.stroke', stylers: [{ color: '#212121' }] }, // Text stroke
    { featureType: 'administrative', elementType: 'geometry', stylers: [{ color: '#757575' }] },
    { featureType: 'road', elementType: 'geometry.fill', stylers: [{ color: '#2c2c2c' }] }, // Roads
    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#000000' }] }, // Water
    { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#1a1a1a' }] }, // Points of interest
    { featureType: 'building', elementType: 'geometry', stylers: [{ color: '#333333' }] } // Ensure buildings are visible
];

const mapOptions = {
    mapTypeId: 'roadmap', // Force roadmap type (supports 3D buildings)
    mapTypeControl: false, // No Map/Satellite toggle
    streetViewControl: false // No Pegman
};
</script>
