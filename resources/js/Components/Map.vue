<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';

defineProps({
    cordinates: {
        type: Object,
        default: { lat: 40.689247, lng: -74.044502 }
    }
});

// Example dark theme style (you can customize this further)
const darkTheme = [
    { elementType: 'geometry', stylers: [{ color: '#212121' }] },
    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#212121' }] },
    {
        featureType: 'administrative',
        elementType: 'geometry',
        stylers: [{ color: '#757575' }]
    },
    {
        featureType: 'road',
        elementType: 'geometry.fill',
        stylers: [{ color: '#2c2c2c' }]
    },
    {
        featureType: 'water',
        elementType: 'geometry',
        stylers: [{ color: '#000000' }]
    }
];
</script>

<template>
    <GoogleMap
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        style="width: 100%; height: 500px"
        :center="cordinates"
        :zoom="15"
        :map-options="{ styles: darkTheme }"
    >
        <Marker :options="{ position: cordinates }" />
    </GoogleMap>
</template>
