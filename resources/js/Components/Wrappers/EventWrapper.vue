<script setup>
import NavLink from '@/Components/NavLink.vue';
import NotifyBotIcon from '@/Components/Icons/NotifyBotIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import { router } from '@inertiajs/vue3';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import { removePostalCode } from '@/Helpers/adressFormatHelper.js';
import { formatDateTime } from '@/Helpers/dateFormatHelper.js';
import AdSense from '@/Components/Elements/AdSense.vue';
import { computed } from 'vue';
import ProcessIcon from '@/Components/Icons/ProcessIcon.vue';
import AddCard from '@/Components/Cards/AddCard.vue';

const props = defineProps({
    events: {
        type: Object,
        required: true,
    },
    add: {
        type: Boolean,
        default: false,
    },
    more: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    owner: {
        type: Boolean,
        default: false,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
    profile: {
        type: Boolean,
        default: false,
    },
    request: {
        type: Boolean,
        default: false,
    },
});

const deleteEvent = (id) => {
    if (confirm('Are you sure you want to delete this event?')) {
        router.delete(route('profile.events.destroy', id), {
            preserveState: false,
            preserveScroll: true,
        });
    }
};

const computedEvents = computed(() => {
    if (props.request || !props.events || props.events.length < 3)
        return props.events;

    const eventsWithAd = [...props.events];

    // ВРЕМЕННЫЙ ПЕРЕКЛЮЧАТЕЛЬ (Поменяйте на true, когда сайт пройдет проверку в Google)
    const showAds = false;

    if (showAds && !props.profile && !props.owner) {
        const randomIndex = Math.floor(
            Math.random() * Math.min(eventsWithAd.length, 8),
        );
        eventsWithAd.splice(randomIndex, 0, {
            isAd: true,
            id: 'ad-block-' + Math.random(),
        });
    }
    return eventsWithAd;
});
</script>
<template>
    <div class="relative">
        <h3 v-if="title" class="text-center">{{ title }}</h3>
        <div
            class="mt-10 grid auto-rows-[600px] gap-y-10 md:auto-rows-[400px] md:grid-cols-2 md:gap-4 lg:grid-cols-4"
        >
            <div
                v-for="event in computedEvents"
                :key="event.id || 'ad-' + Math.random()"
                class="h-[600px] md:h-[400px]"
            >
                <AdSense v-if="event.isAd" />
                <NavLink
                    v-else
                    :href="
                        route(
                            request ? 'profile.event.requests' : 'events.show',
                            event.id,
                        )
                    "
                    class="relative block h-full w-full bg-cover"
                    :style="{ backgroundImage: `url(${event.poster.thumb})` }"
                >
                    <div
                        v-if="
                            owner &&
                            ['pending', 'rejected'].includes(event.status_name)
                        "
                        class="absolute h-full w-full content-center bg-blackTransparent2 text-center"
                        :class="
                            event.status_name === 'pending' ? 'z-20' : 'z-30'
                        "
                    >
                        <div class="bg-blackTransparent2">
                            <h2
                                class="flex items-center justify-center gap-2 capitalize"
                                :class="
                                    event.status_name === 'pending'
                                        ? 'text-green'
                                        : 'text-red'
                                "
                            >
                                {{ event.status_name }}
                                <ProcessIcon
                                    color="#4caf50"
                                    v-if="event.status_name === 'pending'"
                                />
                            </h2>
                            <small v-if="event.status_name === 'pending'">
                                Your event request has been sent for review.
                                <br />
                                You will be notified once it is processed.<br />
                                <b class="text-orange"
                                    >You can also edit your event while it’s
                                    under review.</b
                                >
                            </small>
                            <small v-if="event.status_text" class="mt-2 p-2">
                                Reason: {{ event.status_text }}</small
                            >
                        </div>
                    </div>
                    <div
                        class="absolute inset-0 z-0 brightness-50 backdrop-blur-md"
                    ></div>
                    <img
                        :src="event.poster.thumb"
                        :alt="event.title"
                        class="absolute z-10 h-full w-full object-contain object-center"
                    />
                    <div
                        class="absolute left-0 z-20 flex h-full w-full flex-col justify-between"
                    >
                        <div class="flex h-28 w-full justify-between">
                            <div
                                class="flex h-full w-28 flex-col items-center justify-center bg-orange text-xl"
                            >
                                <p class="text-4xl font-bold">
                                    {{ formatDateTime(event.start_date, 'D') }}
                                </p>
                                <p>
                                    {{
                                        formatDateTime(
                                            event.start_date,
                                            'MMMM',
                                        ).toUpperCase()
                                    }}
                                </p>
                                <small>{{ event.start_time }}</small>
                            </div>
                            <div
                                v-if="
                                    (owner || isAdmin) &&
                                    event.status_name === 'accepted'
                                "
                            >
                                <div
                                    @click.prevent
                                    class="flex cursor-default flex-col gap-y-2 bg-blackTransparent2 p-2"
                                >
                                    <div
                                        v-if="event.notify_count"
                                        tooltip="Sent by bot"
                                        class="flex items-center gap-2"
                                    >
                                        <NotifyBotIcon />
                                        {{ event.notify_count }}
                                    </div>
                                    <div
                                        v-if="event.views_count"
                                        tooltip="Views in rocker"
                                        class="flex items-center gap-2"
                                    >
                                        <EyesIcon />
                                        {{ event.views_count }}
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else-if="
                                    $page.props.auth.user?.settings?.country ===
                                    'all'
                                "
                                class="p-2"
                            >
                                <img
                                    :src="`/icons/${event.country}.png`"
                                    alt="flag"
                                />
                            </div>
                        </div>
                        <div
                            class="z-20 flex h-20 w-full flex-col items-center justify-between bg-gradient-to-t from-black to-transparent"
                        >
                            <div
                                class="absolute bottom-0 z-20 flex h-48 w-full flex-col justify-end bg-gradient-to-t from-black to-transparent pb-2"
                            >
                                <p class="text-center text-xl font-bold">
                                    {{ event.title }}
                                </p>
                                <p class="text-gray-300 text-center">
                                    {{ removePostalCode(event.location, 30) }}
                                </p>
                            </div>
                            <div
                                v-if="(owner || isAdmin) && profile"
                                class="z-20 flex w-full items-center justify-between"
                            >
                                <NavLink
                                    tooltip="Edit"
                                    :href="
                                        route('profile.events.edit', event.id)
                                    "
                                >
                                    <EditIcon />
                                </NavLink>
                                <button
                                    tooltip="Delete"
                                    @click.prevent="deleteEvent(event.id)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <DeleteIcon />
                                </button>
                            </div>
                        </div>
                    </div>
                </NavLink>
            </div>
            <AddCard
                v-if="add && !request"
                title="Add event"
                :href="route('profile.events.create')"
                key="add-event"
            />
        </div>
        <div v-if="more" class="col-span-full py-4 text-center">
            <NavLink
                :href="route('events.index')"
                label="Events list"
                class="font-bold text-orange"
            >
                Explore upcoming concerts
            </NavLink>
        </div>
    </div>
</template>
