<template>
    <GoogleMap
        v-if="mapCenter"
        ref="mapRef"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="mapCenter"
        :zoom="14"
        :styles="darkTheme"
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
        <!-- Всплывающее окно -->
        <InfoWindow
            v-if="openedInfoWindow !== null"
            :options="{
                position: displayedMarkers[openedInfoWindow].position,
                content: getInfoWindowContent(
                    displayedMarkers[openedInfoWindow].info,
                ),
            }"
            :opened="true"
            @close="openedInfoWindow = null"
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

// Генерация HTML-контента для тёмного всплывающего окна
const getInfoWindowContent = (info) => {
    return `
        <div style="
            background-color: #212121;
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        ">
            ${info}
        </div>
    `;
};

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

// Тёмная тема для карты
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
