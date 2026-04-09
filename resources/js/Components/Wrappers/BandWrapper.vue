<script setup>
import { computed, ref } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import AddCard from '@/Components/Cards/AddCard.vue';
import EyesIcon from '@/Components/Icons/EyesIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    bands: {
        type: Object,
    },
    title: {
        type: String,
        default: '',
    },
    genres: {
        type: Array,
        default: () => [],
    },
    owner: {
        type: Boolean,
        default: false,
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
    user: {
        type: Boolean,
        default: false,
    },
    add: {
        type: Boolean,
        default: false,
    },
    more: {
        type: Boolean,
        default: false,
    },
});

const deleteBand = (id) => {
    if (confirm('Are you sure you want to delete this band?')) {
        router.delete(route('profile.bands.destroy', id), {
            preserveState: false,
            preserveScroll: true,
        });
    }
};

const bandsData = computed(() => {
    if (!props.bands) return [];
    if (Array.isArray(props.bands.data)) return props.bands.data;
    if (Array.isArray(props.bands)) return props.bands;
    return [];
});

const wideFlags = ref({});
const onImgLoad = (e, id) => {
    const { naturalWidth, naturalHeight } = e.target;
    wideFlags.value[id] = naturalWidth > naturalHeight;
};
</script>

<template>
    <div class="relative">
        <h3 v-if="title" class="text-center">{{ title }}</h3>

        <div class="relative mt-10">
            <TransitionGroup
                name="grid-animation"
                tag="div"
                class="grid gap-10 md:grid-cols-2 lg:grid-cols-4"
            >
                <NavLink
                    v-for="band in bandsData"
                    :href="route('bands.show', band.slug)"
                    :key="band.id"
                    class="group flex flex-col items-center transition-all duration-500"
                >
                    <div class="relative aspect-square w-full">
                        <div
                            class="h-full w-full overflow-hidden rounded-xl bg-black shadow-lg transition-all duration-300 lg:h-[300px]"
                        >
                            <img
                                :src="band.logo?.svg ?? band.logo.thumb"
                                @load="(e) => onImgLoad(e, band.id)"
                                class="band-img h-full w-full object-center transition-transform duration-500 group-hover:scale-110"
                                :class="
                                    wideFlags[band.id]
                                        ? 'object-contain'
                                        : 'object-cover'
                                "
                                :alt="band.name"
                                @error="$event.target.src = band.logo.original"
                            />
                        </div>
                        <div
                            v-if="owner || isAdmin"
                            class="absolute inset-0 z-10 flex flex-col justify-between rounded-xl bg-blackTransparent2 p-1 md:opacity-100"
                        >
                            <div
                                @click.prevent
                                class="flex justify-end gap-y-2"
                            >
                                <div
                                    tooltip="Views"
                                    class="flex cursor-default items-center gap-x-2 rounded px-2 py-1"
                                >
                                    <EyesIcon />
                                    {{ band.views_count }}
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <NavLink
                                    tooltip="Edit"
                                    :href="route('profile.bands.edit', band.id)"
                                    class="rounded bg-black/50 p-1 transition hover:bg-black/80"
                                >
                                    <EditIcon />
                                </NavLink>
                                <button
                                    tooltip="Delete"
                                    @click.prevent="deleteBand(band.id)"
                                    class="text-red-500 hover:text-red-700 rounded bg-black/50 p-1 transition"
                                >
                                    <DeleteIcon />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 p-2 text-center">
                        <p class="text-pretty text-lg font-semibold">
                            {{
                                band.name.length > 40
                                    ? band.name.slice(0, 40) + '...'
                                    : band.name
                            }}
                        </p>
                    </div>
                </NavLink>

                <AddCard
                    v-if="add"
                    title="Add band"
                    :href="route('profile.bands.create')"
                    key="add-band"
                />
            </TransitionGroup>

            <div
                v-if="bandsData.length === 0"
                class="py-20 text-center text-gray"
            >
                <p class="text-xl opacity-50">No bands found.</p>
            </div>
        </div>

        <div v-if="more" class="py-10 text-center">
            <NavLink
                :href="route('bands.index')"
                label="Bands list"
                class="font-bold text-orange"
            >
                Discover more Armenian bands
            </NavLink>
        </div>
    </div>
</template>

<style scoped>
.grid-animation-move,
.grid-animation-enter-active,
.grid-animation-leave-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.grid-animation-enter-from,
.grid-animation-leave-to {
    opacity: 0;
    transform: scale(0.5) rotateY(30deg) translateY(30px);
    filter: blur(10px);
}

.grid-animation-leave-active {
    position: absolute;
    /* Ensure correct width for absolute items */
}

@media (min-width: 768px) {
    .grid-animation-leave-active {
        width: calc(50% - 1.25rem);
    }
}

@media (min-width: 1024px) {
    .grid-animation-leave-active {
        width: calc(25% - 1.875rem);
    }
}
</style>
