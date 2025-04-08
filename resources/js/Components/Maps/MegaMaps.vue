<template>
    <GoogleMap
        v-if="mapCenter"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="mapCenter"
        :zoom="18"
        :styles="darkTheme"
        class="h-[500px] w-full"
    >
        <Marker
            v-for="(marker, index) in displayedMarkers"
            :key="index"
            :options="{ position: marker }"
        />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';
import { computed } from 'vue';

const props = defineProps({
    markers: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const displayedMarkers = computed(() => {
    // Ограничиваем до 10 меток, берем последние 10
    return props.markers.slice(-10).map((marker) => ({
        lat: +marker.latitude,
        lng: +marker.longitude,
    }));
});

const mapCenter = computed(() => {
    // Центр карты — последняя метка, если массив не пустой
    if (displayedMarkers.value.length > 0) {
        return displayedMarkers.value[displayedMarkers.value.length - 1];
    }
    return { lat: 0, lng: 0 }; // Значение по умолчанию, если меток нет
});

const darkTheme = [
    { elementType: 'geometry', stylers: [{ color: '#212121' }] },
    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] },
    { elementType: 'labels.text.stroke', stylers: [{ color: '#212121' }] },
    {
        featureType: 'administrative',
        elementType: 'geometry',
        stylers: [{ color: '#757575' }],
    },
    {
        featureType: 'road',
        elementType: 'geometry.fill',
        stylers: [{ color: '#2c2c2c' }],
    },
    {
        featureType: 'water',
        elementType: 'geometry',
        stylers: [{ color: '#000000' }],
    },
    {
        featureType: 'poi',
        elementType: 'geometry',
        stylers: [{ color: '#1a1a1a' }],
    },
];
</script>
