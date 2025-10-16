<script setup>
import MainLayout from './MainLayout.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PWAinstall from '@/Components/PWAinstall.vue';
import { isWebApp } from '@/Helpers/setAppUser.js';

const webApp = isWebApp();
</script>

<template>
  <MainLayout>
    <section class="text-white md:pt-6 sm:pt-0 mb-20">
      <MainNavbar v-if="!webApp" />

      <header class="mt-10 text-gray text-center">
        <h1 v-if="$slots.h1">
          <slot name="h1" />
        </h1>
        <h2 class="mb-10" v-if="$slots.header">
          <slot name="header" />
        </h2>
      </header>

      <main class="md:my-20 max-w-screen-sm md:max-w-screen-xl mx-auto">
        <slot />
      </main>
      <PWAinstall />
    </section>
  </MainLayout>
</template>
