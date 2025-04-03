<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
</script>

<template>
    <nav class="flex items-center gap-x-2 mx-auto w-fit">
        <ResponsiveNavLink
            :href="route('logout')"
            method="post"
            as="button"
        >
            Log Out
        </ResponsiveNavLink>
    </nav>
</template>
