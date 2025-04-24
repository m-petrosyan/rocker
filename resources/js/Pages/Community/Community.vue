<script setup>

import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout :meta="{ title: 'Community' }">
        <template #header> Community</template>
        <div>
            <div class="flex flex-col gap-y-2 mt-4">
                <b>Rock Community in Armenia</b>
                <p>
                    Here you'll find useful links and contacts related to the rock and metal music community in
                    Armenia.</p>
                <ul class="list-disc pl-5">
                    <li> Telegram groups and chats</li>
                    <li> Local event organizers</li>
                    <li> Communities and fan groups</li>
                </ul>
                <p>This section is a simple collection of resources for anyone interested in the local scene — from
                    regular listeners to musicians and organizers.</p>
                <p>If you know a group, chat, or organizer that should be listed here, feel free to reach out.</p>
            </div>
            <div class="flex flex-col gap-y-2 mt-4">
                <b>Telegram groups and chats</b>
                <ul class="list-disc pl-5">
                    <li><a href="https://t.me/yerevanmetal" target="_blank">Rock Metal Yerevan (Telegram Group – Yerevan
                        Metal
                        Announcements, discussions, and community updates.)</a></li>
                    <li><a href="https://t.me/gyumrimetal" target="_blank">Rock Metal Gyumri</a></li>
                </ul>
            </div>
        </div>
    </GuestLayout>
</template>
