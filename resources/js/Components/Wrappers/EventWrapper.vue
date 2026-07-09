<script setup>
import NavLink from '@/Components/NavLink.vue';
import NotifyBotIcon from '@/Components/Icons/NotifyBotIcon.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import { router } from '@inertiajs/vue3';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import LocationIcon from '@/Components/Icons/LocationIcon.vue';
import TicketIcon from '@/Components/Icons/TicketIcon.vue';
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
            class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        >
            <div
                v-for="event in computedEvents"
                :key="event.id || 'ad-' + Math.random()"
                class="min-h-[430px]"
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
                    class="group relative block h-full min-h-[430px] overflow-hidden rounded-lg border border-white/10 bg-[#050505] shadow-[0_18px_45px_rgba(0,0,0,0.45)] transition duration-300 hover:-translate-y-1 hover:border-orange/70 hover:shadow-[0_22px_60px_rgba(255,87,34,0.16)]"
                >
                    <div
                        v-if="
                            owner &&
                            ['pending', 'rejected'].includes(event.status_name)
                        "
                        class="absolute inset-0 z-40 flex items-center justify-center bg-blackTransparent text-center"
                    >
                        <div class="bg-blackTransparent2 px-4 py-5">
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
                                    >You can also edit your event while it is
                                    under review.</b
                                >
                            </small>
                            <small v-if="event.status_text" class="mt-2 block p-2">
                                Reason: {{ event.status_text }}</small
                            >
                        </div>
                    </div>

                    <img
                        :src="event.poster.thumb"
                        :alt="event.title"
                        class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-gradient-to-b from-black/25 via-black/10 to-black/95"></div>
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black via-black/85 to-transparent"></div>

                    <div
                        class="absolute left-0 top-0 z-20 flex h-[92px] w-[72px] flex-col items-center justify-center rounded-br-lg bg-orange text-white shadow-lg"
                    >
                        <p class="text-4xl font-black leading-none">
                            {{ formatDateTime(event.start_date, 'D') }}
                        </p>
                        <p class="mt-1 text-xs font-bold leading-none">
                            {{
                                formatDateTime(
                                    event.start_date,
                                    'MMMM',
                                ).toUpperCase()
                            }}
                        </p>
                        <small class="mt-1 text-[11px] font-semibold leading-none">
                            {{ event.start_time }}
                        </small>
                    </div>

                    <div class="absolute right-3 top-3 z-20 flex flex-col items-end gap-2">
                        <div
                            v-if="event.end_date"
                            class="rounded-md bg-orange px-2 py-1 text-xs font-black uppercase tracking-wide text-white shadow-lg"
                        >
                            Fest
                        </div>
                        <div
                            v-if="event.country"
                            class="rounded-md bg-white p-1 shadow-lg"
                        >
                            <img
                                :src="`/icons/${event.country}.png`"
                                alt="flag"
                                class="h-6 w-6 object-contain"
                            />
                        </div>
                        <div
                            v-if="
                                (owner || isAdmin) &&
                                event.status_name === 'accepted'
                            "
                            @click.prevent
                            class="flex cursor-default flex-col gap-y-1 rounded-md bg-black/65 px-2 py-1 text-xs backdrop-blur"
                        >
                            <div
                                v-if="event.notify_count"
                                tooltip="Sent by bot"
                                class="flex items-center gap-1"
                            >
                                <NotifyBotIcon class="h-4 w-4" />
                                {{ event.notify_count }}
                            </div>
                            <div
                                v-if="event.views_count"
                                tooltip="Views in rocker"
                                class="flex items-center gap-1"
                            >
                                <EyesIcon class="h-4 w-4" />
                                {{ event.views_count }}
                            </div>
                        </div>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 z-20 p-4">
                        <p
                            v-if="event.genre"
                            class="mb-1 text-xs font-black uppercase tracking-normal text-orange"
                        >
                            {{ event.genre }}
                        </p>
                        <p
                            v-if="event.end_date"
                            class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-orange-200"
                        >
                            {{ formatDateTime(event.start_date, 'DD MMM') }} - {{ formatDateTime(event.end_date, 'DD MMM') }}
                        </p>
                        <h4
                            class="max-h-[48px] overflow-hidden text-[22px] font-black uppercase leading-[1.05] text-white drop-shadow"
                        >
                            {{ event.title }}
                        </h4>

                        <div class="mt-4 grid grid-cols-[1fr_auto] gap-5 border-t border-white/10 pt-3 text-[11px] leading-tight text-white/80">
                            <div class="flex min-w-0 items-start gap-1 items-center">
                                <LocationIcon class="mt-0.5 h-5 w-5 shrink-0 opacity-70" />
                                    <p class="truncate font-bold text-white px-1">
                                        {{ removePostalCode(event.location, 32) }}
                                    </p>
                            </div>
                            <div
                                v-if="event.price || event.ticket"
                                class="flex max-w-[112px] items-start items-center gap-1 text-right"
                            >
                                <TicketIcon class="mt-0.5 h-5 w-5 shrink-0 opacity-70" />
                                    <p class="font-bold text-white px-1 truncate">
                                        {{ event.price || 'Tickets' }}
                                    </p>
                            </div>
                        </div>

                        <div
                            v-if="(owner || isAdmin) && profile"
                            class="mt-3 flex w-full items-center justify-between border-t border-white/10 pt-2"
                            @click.prevent
                        >
                            <NavLink
                                tooltip="Edit"
                                :href="route('profile.events.edit', event.id)"
                                class="rounded bg-white/10 p-1.5 hover:bg-white/20"
                            >
                                <EditIcon />
                            </NavLink>
                            <button
                                tooltip="Delete"
                                @click.prevent="deleteEvent(event.id)"
                                class="rounded bg-white/10 p-1.5 text-red-500 hover:bg-white/20 hover:text-red-400"
                            >
                                <DeleteIcon />
                            </button>
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
