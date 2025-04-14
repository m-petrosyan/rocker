<script setup>
import moment from 'moment-timezone';
import NavLink from '@/Components/NavLink.vue';

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
    }
});
</script>
<template>
    <div class="relative">
        <div class="grid gap-y-10 md:grid-cols-2 md:gap-4 lg:grid-cols-4 auto-rows-[600px] md:auto-rows-[400px]">
            <div
                v-for="event in events.data"
                :key="event.id"
                class="h-[600px] md:h-[400px]"
            >
                <NavLink
                    :href="route('events.show', event.id)"
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
                        class="absolute left-0 z-20 flex h-28 w-28 flex-col items-center justify-center bg-orange text-xl"
                    >
                        <p class="text-4xl font-bold">
                            {{ moment(event.start_date, 'DD.MM.YY').format('D').toUpperCase() }}
                        </p>
                        <p>
                            {{ moment(event.start_date, 'DD.MM.YY').format('MMMM').toUpperCase() }}
                        </p>
                        <small>{{ event.start_time }}</small>
                    </div>
                    <div
                        class="absolute bottom-0 z-20 h-48 w-full bg-gradient-to-t from-black to-transparent md:h-52"
                    >
                        <h3 class="pt-20 text-center text-2xl">{{ event.title }}</h3>
                        <p class="text-gray-300 text-center">{{ event.location }}</p>
                    </div>
                </NavLink>
            </div>
            <NavLink
                v-if="add"
                :href="route('profile.events.create')"
                class="flex h-[600px] items-center gap-2 border-2 border-dashed border-graydark2 p-4 hover:border-orange hover:bg-graydark2 md:h-[400px]"
            >
                <div class="mx-auto flex w-32 flex-col items-center gap-y-4 rounded-lg p-4">
                    <h2 class="text-3xl">+</h2>
                    <h3>Add event</h3>
                </div>
            </NavLink>
        </div>
        <div v-if="more"
             class="col-span-full text-center py-4">
            <NavLink :href="route('events.index', { page: events.current_page + 1 })"
                     class="text-orange font-bold">
                See more
            </NavLink>
        </div>
    </div>
</template>
