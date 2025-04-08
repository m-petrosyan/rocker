<template>
    <GoogleMap
        v-if="mapCenter"
        ref="mapRef"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="mapCenter"
        :zoom="18"
        :styles="darkTheme"  <!-- Возвращаем тёмный стиль -->
    class="w-full h-[500px]"
    @mounted="onMapMounted"
    >
    <Marker
        v-for="(marker, index) in displayedMarkers"
        :key="index"
        :options="{
                position: marker,
                icon: '/images/logo.png'
            }"
    />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    markers: {
        type: Array,
        required: true,
        default: () => []
    }
});

const mapRef = ref(null);
const mapInstance = ref(null);

const displayedMarkers = computed(() => {
    return props.markers.slice(-10).map(marker => ({
        lat: +marker.latitude,
        lng: +marker.longitude
    }));
});

const mapCenter = computed(() => {
    if (displayedMarkers.value.length > 0) {
        return displayedMarkers.value[0];
    }
    return { lat: 0, lng: 0 };
});

const onMapMounted = (map) => {
    mapInstance.value = map;
    fitMapToMarkers();
};

const fitMapToMarkers = () => {
    if (mapInstance.value && displayedMarkers.value.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        displayedMarkers.value.forEach(marker => {
            bounds.extend(new google.maps.LatLng(marker.lat, marker.lng));
        });
        mapInstance.value.fitBounds(bounds);
    }
};

watch(() => props.markers, () => {
    fitMapToMarkers();
}, { deep: true });

// Тёмная тема
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
