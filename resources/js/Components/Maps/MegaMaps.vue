<template>
    <GoogleMap
        v-if="mapCenter"
        ref="mapRef"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="mapCenter"
        :zoom="14"
        class="h-[500px] w-full"
        @mounted="onMapMounted"
    >
        <!-- Метки на карте -->
        <Marker
            v-for="(marker, index) in displayedMarkers"
            :key="index"
            :options="{
                position: marker.position,
                icon: '/images/logo.png',
            }"
            @click="openInfoWindow(index)"
        />
        <!-- Всплывающие окна -->
        <InfoWindow
            v-for="(marker, index) in displayedMarkers"
            :key="`info-${index}`"
            :options="{
                position: marker.position,
                content: marker.info,
            }"
            :opened="openedInfoWindow === index"
        />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, InfoWindow, Marker } from 'vue3-google-map';
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps({
    markers: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const mapRef = ref(null);
const mapInstance = ref(null);
const openedInfoWindow = ref(null);

// Форматируем метки для карты
const displayedMarkers = computed(() => {
    return props.markers.map((marker) => ({
        position: {
            lat: parseFloat(marker.latitude),
            lng: parseFloat(marker.longitude),
        },
        info: marker.info || 'Информация отсутствует',
    }));
});

// Центр карты — первая метка
const mapCenter = computed(() => {
    if (displayedMarkers.value.length > 0) {
        return displayedMarkers.value[0].position;
    }
    return { lat: 0, lng: 0 };
});

// Открытие всплывающего окна
const openInfoWindow = (index) => {
    openedInfoWindow.value = index;
};

// Инициализация карты
const onMapMounted = (map) => {
    mapInstance.value = map;
    fitMapToMarkers();
};

// Подстройка масштаба под все метки
const fitMapToMarkers = () => {
    if (mapInstance.value && displayedMarkers.value.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        displayedMarkers.value.forEach((marker) => {
            bounds.extend(
                new google.maps.LatLng(
                    marker.position.lat,
                    marker.position.lng,
                ),
            );
        });
        mapInstance.value.fitBounds(bounds);
    }
};

// Обновление масштаба при изменении меток
watch(
    () => props.markers,
    () => {
        nextTick(() => {
            fitMapToMarkers();
        });
    },
    { deep: true },
);
</script>
