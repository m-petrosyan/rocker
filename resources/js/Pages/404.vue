<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import NavLink from '@/Components/NavLink.vue';
</script>

<template>
    <GuestLayout :meta="{title: '404 page not found'}">
        <template #header><span>404 page not found </span></template>
        <template #h1>Armenian Rock & Metal Bands, Concerts, Galleries</template>
        <div class="flex flex-col justify-center h-full items-center text-center">
            <div class="flex justify-center items-center">
                <img src="/images/404.gif" alt="404 page not found" />
            </div>

            <h3 class="text-orange">We lost this page</h3>
            <p> We searched high and low but couldn’t find what you’re looking for. Let’s find a better place for you to
                go.</p>

            <NavLink :href="route('home')"
                     label="Events list"
                     class="text-orange font-bold mt-10">
                Home page
            </NavLink>
        </div>
    </GuestLayout>
</template>
