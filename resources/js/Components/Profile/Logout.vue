<script setup>
import LogoutIcon from '@/Components/Icons/LogoutIcon.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    owner: {
        type: Boolean,
        required: true
    }
});
</script>

<template>
    <div v-if="owner" class="absolute right-0 top-0">
        <div v-tooltip="'Logout'">
            <ResponsiveNavLink
                :href="route('logout')"
                method="post"
                as="button"
            >
                <LogoutIcon />
            </ResponsiveNavLink>
        </div>
    </div>
</template>
