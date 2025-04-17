<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';

defineProps({
    events: {
        type: Object
    }
});
</script>

<template>
    <GuestLayout :meta="{ title: 'Events' }">
        <template #header> Events</template>
        <!--        <MegaMaps-->
        <!--            :markers="[-->
        <!--                { latitude: 40.188067, longitude: 44.5139577, info: 'Tonlab' },-->
        <!--                {-->
        <!--                    latitude: 40.1800023,-->
        <!--                    longitude: 44.5160956,-->
        <!--                    info: 'The venue',-->
        <!--                },-->
        <!--                {-->
        <!--                    latitude: 40.1895131,-->
        <!--                    longitude: 44.521072,-->
        <!--                    info: 'Stop club',-->
        <!--                },-->
        <!--                {-->
        <!--                    latitude: 40.1800023,-->
        <!--                    longitude: 44.5160956,-->
        <!--                    info: 'Tonlab2',-->
        <!--                },-->
        <!--                {-->
        <!--                    latitude: 40.1737167,-->
        <!--                    longitude: 44.516279399999995,-->
        <!--                    info: 'Hardrock',-->
        <!--                },-->
        <!--            ]"-->
        <!--        />-->
        <!--        <MultiSelect class="w-48" />-->
        <EventWrapper :events add />
    </GuestLayout>
</template>
