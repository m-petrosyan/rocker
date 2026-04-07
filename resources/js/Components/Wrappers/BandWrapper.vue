<script setup>
import { computed, ref } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import AddCard from '@/Components/Cards/AddCard.vue';

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
