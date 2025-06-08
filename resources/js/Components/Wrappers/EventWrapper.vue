<script setup>
import moment from 'moment-timezone';
import NavLink from '@/Components/NavLink.vue';
import NotifyIcon from '@/Components/Icons/NotifyIcon.vue';
import { removePostalCode } from '@/Helpers/adressFormatHelper.js';

defineProps({
    events: {
        type: Object,
        required: true
    },
    add: {
        type: Boolean,
        default: false
    },
    more: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    owner: {
        type: Boolean,
        default: false
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
});
</script>
<template>
    <div class="relative">
        <h2 v-if="title" class="text-center">{{ title }}</h2>
        <div class="mt-10 grid gap-y-10 md:grid-cols-2 md:gap-4 lg:grid-cols-4 auto-rows-[600px] md:auto-rows-[400px]">
            <div
                v-for="event in events"
                :key="event.id"
                class="h-[600px] md:h-[400px]"
            >
                <NavLink
                    :href="route('events.show', event?.event_id ?? event.id)"
                    class="relative h-full w-full block"
                    :style="{ backgroundImage: `url(${event.poster})` }"
                >
                    <div class="absolute inset-0 z-0 brightness-50 backdrop-blur-md"></div>
                    <img
                        :src="event.poster"
                        :alt="event.title"
                        class="absolute z-10 h-full w-full object-contain object-center"
                    />
                    <div
                        class="absolute left-0 z-20 flex h-28 w-full flex justify-between ">
                        <div class="w-28 h-full flex flex-col items-center justify-center bg-orange text-xl">
                            <p class="text-4xl font-bold">
                                {{ moment(event.start_date, 'DD.MM.YY').format('D').toUpperCase() }}
                            </p>
                            <p>
                                {{ moment(event.start_date, 'DD.MM.YY').format('MMMM').toUpperCase() }}
                            </p>
                            <small>{{ event.start_time }}</small>
                        </div>
                        <div v-if="owner || isAdmin" class="flex flex-col items-center justify-center">
                            <!--                            <div class="flex items-center gap-2">-->
                            <!--                                <EyesIcon />-->
                            <!--                                {{ event.notify_count }}-->
                            <!--                            </div>-->
                            <div class="flex items-center gap-2">
                                <NotifyIcon />
                                {{ event.notify_count }}
                            </div>
                        </div>
                    </div>
                    <div
                        class="absolute pb-2 flex flex-col justify-end h-48 bottom-0 z-20 w-full bg-gradient-to-t from-black to-transparent ">
                        <h3 class="text-center text-2xl">{{ event.title }}</h3>
                        <p class="text-gray-300 text-center">{{ removePostalCode(event.location, 30) }}</p>
                    </div>
                </NavLink>
            </div>
            <NavLink
                v-if="add"
                :href="route('profile.events.create')"
                class="flex h-[600px] items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2 md:h-[400px]"
            >
                <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg">
                    <h2 class="text-3xl">+</h2>
                    <h3>Add event</h3>
                </div>
            </NavLink>
        </div>
        <div v-if="more"
             class="col-span-full text-center py-4">
            <NavLink :href="route('events.index')"
                     class="text-orange font-bold">
                See more
            </NavLink>
        </div>
    </div>
</template>
