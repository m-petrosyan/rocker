<script setup>
import MainNavbar from '@/Components/Nav/MainNavbar.vue';
import PwaNavbar from '@/Components/Nav/PwaNavbar.vue';
import { Head, usePage } from '@inertiajs/vue3';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';

defineProps({
  title: {
    type: String,
    required: false,
    default: 'rocker'
  }
});

const page = usePage();
</script>

<template>
  <Head :title />
  <MainNavbar v-if="page.url !== '/' && !$isPWA" />
  <div class="mt-20 mx-auto w-1/3 rounded-lg bg-graydark p-6">
    <ErrorMessages :messages="$page.props.errors" />
    <slot />
  </div>
  <PwaNavbar v-if="page.url !== '/' && $isPWA" />
</template>
