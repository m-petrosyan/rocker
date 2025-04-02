<template>
    <GoogleMap
        v-if="center.lat && center.lng"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="center"
        :zoom="15"
        :styles="darkTheme"
        :map-options="mapOptions"
        style="width: 100%; height: 500px"
    >
        <Marker :options="{ position: center }" />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';


const props = defineProps({
    cordinates: {
        type: Object,
        required: true,
        default: () => ({ latitude: 40.689247, longitude: -74.044502 }) //
    }
});

const center = {
    lat: 40.689247,
    lng: -74.044502
};

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

const mapOptions = {
    styles: darkTheme, // Include styles here too for compatibility
    mapTypeControl: false, // Removes Map/Satellite toggle
    streetViewControl: false // Removes Pegman (Street View icon)
};
</script>
