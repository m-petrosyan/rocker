<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';

defineProps({
    auth: {
        type: Object,
        required: true
    },
    events: {
        type: Object,
        required: true
    },
    galleries: {
        type: Object,
        required: true
    },
    bands: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <GuestLayout :meta="{title: 'Armenian Rock & Metal Portal'}">

        <template #h1>Armenian Rock & Metal Bands, Concerts, Galleries</template>
        <h2 class="text-pretty text-lg font-medium text-gray mb-6">Welcome to Rocker.am – your portal for Armenian rock
            and
            metal bands.
            Discover
            upcoming
            concerts, explore
            photo galleries from live performances, and stay updated with the Armenian music scene.</h2>

        <EventWrapper :events="events.data" more title="Events" />
        <BandWrapper :bands="bands.data" more title="Bands" />
        <GalleryWrapper :galleries="galleries.data" more title="Galleries" />
        <section class="my-20">
            <h2 class="text-pretty text-gray mb-6 text-[16px] font-medium">The Armenian Rock & Metal Scene</h2>
            <p class="text-gray ">
                Armenia has a unique and vibrant rock and metal culture that blends local
                traditions with international influences. From the early underground
                movements in Yerevan to modern-day festivals, Armenian bands have built a
                reputation for powerful live shows and original sound. Rocker.am is here to
                showcase this culture, helping fans discover both legendary and emerging
                artists.
            </p>
        </section>
    </GuestLayout>
</template>
