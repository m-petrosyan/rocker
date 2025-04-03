<template>
    <GoogleMap
        v-if="center"
        api-key="AIzaSyCovr1rcKSduU9SLpe_IX-EzuF-_sVVAlY"
        :center="center"
        :zoom="18"
        :styles="darkTheme"
        :map-options="mapOptions"
        style="width: 100%; height: 500px"
    >
        <Marker :options="{ position: center }" />
    </GoogleMap>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map';
import { computed } from 'vue';

const props = defineProps({
    cordinates: {
        type: Object,
        required: true
    }
});

const center = computed(() => {
    return {
        lat: +props.cordinates?.latitude,
        lng: +props.cordinates?.longitude
    };
});

const darkTheme = [
    { elementType: 'geometry', stylers: [{ color: '#212121' }] }, // Фон
    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] }, // Убираем иконки
    { elementType: 'labels.text.fill', stylers: [{ color: '#757575' }] }, // Цвет текста
    { elementType: 'labels.text.stroke', stylers: [{ color: '#212121' }] }, // Обводка текста
    { featureType: 'administrative', elementType: 'geometry', stylers: [{ color: '#757575' }] },
    { featureType: 'road', elementType: 'geometry.fill', stylers: [{ color: '#2c2c2c' }] }, // Дороги
    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#000000' }] }, // Вода
    { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#1a1a1a' }] }, // Точки интереса
    { featureType: 'building', elementType: 'geometry', stylers: [{ color: '#444444' }] } // Силуэты зданий
];

const mapOptions = {
    mapTypeId: 'roadmap', // Тип карты для отображения зданий
    mapTypeControl: false, // Без переключения типов
    streetViewControl: false, // Без Pegman
    tilt: 45 // Наклон для 3D-видимости зданий
};
</script>
