<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';

defineProps({
    cordinates: {
        type: Object,
        default: { lat: 40.689247, lng: -74.044502 }
    }
});

</script>

<template>
    <GoogleMap
        styles="dark"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        style="width: 100%; height: 500px"
        :center="center"
        :zoom="15"
    >
        <Marker :options="{ position: cordinates }" />
    </GoogleMap>
</template>
