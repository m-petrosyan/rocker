<script setup>
import LogoutIcon from '@/Components/Icons/LogoutIcon.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  owner: {
    type: Boolean,
    required: false,
    default: false
  }
});
</script>

<template>
  <div v-if="owner" class="">
    <div tooltip="Logout">
      <Link
        :href="route('logout')"
        method="post"
        as="button"
      >
        <LogoutIcon />
      </Link>
    </div>
  </div>
</template>
