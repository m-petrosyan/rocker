<script setup>
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

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

const ssr = computed(() => {
  return typeof window === 'undefined';
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
    <section v-if="!ssr" class="my-20">
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

    <section v-else class="my-20">
      <div>
        <h2 class="text-pretty text-gray mb-6 text-[16px] font-medium">The Armenian Rock & Metal Scene</h2>
        <p class="text-gray">
          Armenia’s rock and metal music culture thrives on a unique fusion of local traditions and global
          influences. Emerging from underground movements in the capital city during the late 20th century,
          this
          scene has evolved into a dynamic force known for its passionate live performances and distinctive
          sound.
          Rocker.am serves as a hub for fans, offering insights into both established and emerging artists
          shaping
          this vibrant community.
        </p>
        <p class="text-gray">
          The Armenian music scene is celebrated for its diversity, blending heavy riffs with melodic elements
          rooted in cultural heritage. Annual festivals and concerts draw thousands of enthusiasts, showcasing
          the
          energy and creativity of local musicians. From intimate gigs to large-scale events, the atmosphere
          is
          electric, with fans uniting to celebrate their shared love for rock and metal music.
        </p>
        <p class="text-gray">
          Rocker.am provides a platform to explore this culture through detailed artist profiles, event
          schedules,
          and exclusive photo galleries. These galleries capture the raw intensity of live performances,
          allowing
          fans to relive unforgettable moments. Whether you’re a dedicated follower or new to the scene, our
          portal keeps you informed about the latest concerts and music releases in Armenia.
        </p>
        <p class="text-gray">
          Beyond music, the Armenian rock and metal community fosters a sense of belonging. Fans and musicians
          connect through shared experiences at concerts and festivals, creating lasting memories. Rocker.am
          supports this community by offering a space to discover new talent, explore event highlights, and
          stay
          updated on the evolving music landscape.
        </p>
      </div>

      <div class="my-20">
        <h2 class="text-pretty text-gray mb-6 text-[16px] font-medium">A Legacy of Rock and Metal in
          Armenia</h2>
        <p class="text-gray">
          The roots of Armenian rock and metal trace back to the Soviet era, when musicians began
          experimenting
          with Western influences. By the 1990s, the scene gained momentum, with artists blending traditional
          melodies with heavy metal and alternative rock. This legacy continues to inspire modern musicians
          who
          push creative boundaries while honoring their cultural roots.
        </p>
        <p class="text-gray">
          Today, Armenia’s music culture is a vibrant mix of innovation and tradition. Festivals and live
          events
          provide platforms for artists to showcase their talent, drawing diverse audiences from across the
          region. Rocker.am is dedicated to preserving this legacy, offering fans a comprehensive resource to
          explore the history and future of Armenian rock and metal music.
        </p>
        <p class="text-gray">
          Our mission is to connect fans with the music they love. From discovering new artists to reliving
          iconic
          performances through photos, Rocker.am is your gateway to the Armenian rock and metal scene. Join us
          to
          stay updated on upcoming events and immerse yourself in the passion and creativity of this unique
          music
          culture.
        </p>
      </div>
    </section>

  </GuestLayout>
</template>
