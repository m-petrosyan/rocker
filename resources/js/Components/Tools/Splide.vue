<script setup>
import { Splide, SplideSlide } from '@splidejs/vue-splide';

defineProps({
  images: {
    type: Array,
    required: true
  }
});

const options = {
  perPage: 1,
  lazyLoad: 'nearby',
  interval: 25000,
  speed: 1000,
  autoplay: true,
  pauseOnHover: true,
  width: '80%'
};
</script>

<template>
  <Splide :options="options" aria-label="Images" class="mx-auto">
    <SplideSlide v-for="(image, index) in images" :key="index">
      <img :src="image.large" class="w-full h-auto" alt="image" />
    </SplideSlide>
  </Splide>
</template>

<style lang="scss">
.splide__arrow {
  background: none !important;

  svg {
    fill: theme('colors.orange') !important;
  }
}
</style>