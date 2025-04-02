<template>
    <GoogleMap
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="center"
        :zoom="15"
        :styles="darkTheme"
        style="width: 100%; height: 500px"
    >
        <Marker :options="{ position: center }" />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';

// Define center coordinates
const center = { lat: 40.689247, lng: -74.044502 };

// Hardcoded dark theme styles
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
