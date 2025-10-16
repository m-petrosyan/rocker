<script setup>
import MainLayout from './MainLayout.vue';
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import { isWebApp } from '@/Helpers/setAppUser.js';

const webApp = isWebApp();

defineProps({
  meta: Object
});
</script>

<template>
  <MainLayout v-bind="$props">
    <section class="text-white pt-6 sm:pt-0 mb-20">
      <MainNavbar v-if="!webApp" />

      <main
        class="mt-20 sm:mx-auto w-full sm:w-8/12 md:w-4/12 md:max-w-[500px]
               rounded-lg bg-graydark2 p-6"
      >
        <ErrorMessages :messages="$page.props.errors" />
        <slot />
      </main>

      <slot name="underslot" />
    </section>
  </MainLayout>
</template>
