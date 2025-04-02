<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';

// Define coordinates (ensure this is reactive or passed correctly)
const cordinates = { lat: 40.689247, lng: -74.044502 };

// Dark theme styles (JSON array for Google Maps)
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

<template>
    <GoogleMap
        api-key="YOUR_API_KEY"
        style="width: 100%; height: 500px"
        :center="cordinates"
        :zoom="15"
        :map-options="{ styles: darkTheme }"
    >
        <Marker :options="{ position: cordinates }" />
    </GoogleMap>
</template>
