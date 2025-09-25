<script setup>

import InstagramIcon from '@/Components/Icons/InstagramIcon.vue';
import BotIcon from '@/Components/Icons/BotIcon.vue';
</script>

<template>
    <footer>
        <div class="flex justify-center md:justify-end pr-3 pb-1">
            <div class="relative flex items-center flex-col md:flex-row justify-between w-full pl-2 text-graydarker">
                <p class="order-1 text-center"> © {{ new Date().getFullYear() }} rocker.am. Some rights reserved. |
                    <a href="mailto:info@rocker.am">info@rocker.am</a></p>

                <div
                    class="order-0 md:order-1 py-2 md:py-0 md:absolute left-1/2  bottom-14 md:bottom-0 flex gap-x-2">
                    <a href="https://www.instagram.com/rocker._.am/"
                       aria-label="Rocker.am Instagram"
                       target="_blank">
                        <InstagramIcon />
                    </a>
                    <a href="https://t.me/RockMetalEventsbot"
                       aria-label="Rocker.am Telegram Bot"
                       rel="nofollow noopener"
                       target="_blank">
                        <BotIcon />
                    </a>
                </div>
                <a class="order-3" href="https://t.me/mpetrosyan1" target="_blank"
                   aria-label="Miqayel Petrosyan Telegram"
                   rel="nofollow noopener">
                    <small>Developed by Miqayel Petrosyan</small>
                </a>
            </div>
        </div>
    </footer>
</template>
