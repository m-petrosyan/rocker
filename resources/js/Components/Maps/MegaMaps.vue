<template>
    <GoogleMap
        v-if="mapCenter"
        ref="mapRef"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="mapCenter"
        :zoom="18"
        class="h-[500px] w-full"
        @mounted="onMapMounted"
    >
        <Marker
            v-for="(marker, index) in displayedMarkers"
            :key="index"
            :options="{
                position: marker,
                icon: '/images/logo.png', // Путь к файлу иконки
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
        default: () => [],
    },
});

const mapRef = ref(null);
const mapInstance = ref(null);

// Преобразование меток в формат { lat, lng }
const displayedMarkers = computed(() => {
    return props.markers.slice(-10).map((marker) => ({
        lat: +marker.latitude,
        lng: +marker.longitude,
    }));
});

// Временный центр карты
const mapCenter = computed(() => {
    if (displayedMarkers.value.length > 0) {
        return displayedMarkers.value[0];
    }
    return { lat: 0, lng: 0 };
});

// Инициализация карты
const onMapMounted = (map) => {
    mapInstance.value = map;
    fitMapToMarkers();
};

// Настройка границ карты
const fitMapToMarkers = () => {
    if (mapInstance.value && displayedMarkers.value.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        displayedMarkers.value.forEach((marker) => {
            bounds.extend(new google.maps.LatLng(marker.lat, marker.lng));
        });
        mapInstance.value.fitBounds(bounds);
    }
};

// Обновление карты при изменении меток
watch(
    () => props.markers,
    () => {
        fitMapToMarkers();
    },
    { deep: true },
);
</script>
