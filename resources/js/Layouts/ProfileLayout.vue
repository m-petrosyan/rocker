<script setup>
import MainLayout from './MainLayout.vue';
import { isWebApp } from '@/Helpers/setAppUser.js';

const webApp = isWebApp();
</script>

<template>
  <MainLayout>
    <section class="min-h-screen bg-black text-white pt-6 sm:pt-0 pb-40">
      <!--      <MainNavbar v-if="!webApp" />-->

      <header v-if="$slots.header" class="my-10 text-center text-gray">
        <h2 class="mb-5">
          <slot name="header" />
        </h2>
      </header>

      <main
        class="profile relative my-12 md:my-28 bg-graydark py-2 sm:px-6 lg:p-4 md:px-0 max-w-screen-sm md:max-w-screen-xl mx-auto"
      >
        <slot />
      </main>
    </section>
  </MainLayout>
</template>
