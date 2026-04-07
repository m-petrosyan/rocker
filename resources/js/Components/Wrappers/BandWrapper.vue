<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import AddCard from '@/Components/Cards/AddCard.vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    bands: {
        type: Object,
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
});

const selectedGenreSlug = ref(null);
const page = usePage();

const syncGenreFromUrl = () => {
    const urlParams = new URLSearchParams(window.location.search);
    selectedGenreSlug.value = urlParams.get('genre');
};

onMounted(() => {
    syncGenreFromUrl();
});

// Следим за изменениями URL (например, при нажатии кнопки "Назад" в браузере)
watch(
    () => page.url,
    () => {
        syncGenreFromUrl();
    },
);

const filteredBands = computed(() => {
    if (!selectedGenreSlug.value) return props.bands.data;
    return props.bands.data.filter((band) =>
        band.genres.some(
            (genre) =>
                genre.slug === selectedGenreSlug.value ||
                genre.id.toString() === selectedGenreSlug.value,
        ),
    );
});

const selectGenre = (genre) => {
    // Если передали null или объект с id: null (сброс фильтра)
    if (!genre || genre.id === null) {
        selectedGenreSlug.value = null;
    } else {
        const identifier = genre.slug || genre.id.toString();
        selectedGenreSlug.value =
            selectedGenreSlug.value === identifier ? null : identifier;
    }

    // Обновляем URL
    const url = new URL(window.location.href);
    if (selectedGenreSlug.value) {
        url.searchParams.set('genre', selectedGenreSlug.value);
    } else {
        url.searchParams.delete('genre');
    }

    // Используем Inertia router для обновления URL
    router.get(
        url.pathname,
        { genre: selectedGenreSlug.value || undefined },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const wideFlags = ref({});
const onImgLoad = (e, id) => {
    const { naturalWidth, naturalHeight } = e.target;
    wideFlags.value[id] = naturalWidth > naturalHeight;
};
</script>

<template>
    <GuestLayout
        :meta="{
            title: 'Bands - Discover Armenian rock and metal bands',
            description:
                'Discover Armenian rock and metal bands. Explore profiles, genres, photos, and stories from the Yerevan rock and metal scene on Rocker.am.',
            keywords:
                'armenian rock bands, armenian metal bands, yerevan rock scene, armenian hard rock, armenian heavy metal, armenian punk, armenian alternative rock, armenian folk metal, armenian thrash metal, armenian progressive rock, armenian doom metal, armenian post-rock, armenian gothic rock, armenian underground music' +
                ', ' +
                bands.data.map((band) => band.name).join(', '),
        }"
    >
        <template #header> Bands</template>
        <div class="text-pretty text-gray">
            <h2 class="mb-6 text-lg font-medium text-gray text-orange/80">
                Armenian Rock & Metal Bands
            </h2>
            <p>
                Discover the best Armenian rock and metal bands on
                <strong>Rocker.am</strong>. From legendary pioneers of the
                Yerevan scene to emerging artists, explore the musicians shaping
                Armenia’s rock and metal sound.
            </p>
        </div>

        <!-- Genre Filter -->
        <div class="mt-8 flex flex-wrap gap-2">
            <button
                @click="selectGenre({ id: null, slug: null })"
                class="rounded-full border px-4 py-1.5 text-sm font-medium transition-all duration-300"
                :class="
                    !selectedGenreSlug
                        ? 'border-orange bg-orange text-white'
                        : 'border-graydark2 bg-graydark2 text-gray hover:border-gray'
                "
            >
                All Genres
            </button>
            <button
                v-for="genre in genres"
                :key="genre.id"
                @click="selectGenre(genre)"
                class="rounded-full border px-4 py-1.5 text-sm font-medium transition-all duration-300"
                :class="
                    selectedGenreSlug === (genre.slug || genre.id.toString())
                        ? 'border-orange bg-orange text-white shadow-lg shadow-orange/20'
                        : 'border-graydark2 bg-graydark2 text-gray hover:border-gray'
                "
            >
                {{ genre.name }}
            </button>
        </div>

        <template #h1> Discover Armenian rock and metal bands</template>

        <div class="relative mt-10">
            <TransitionGroup
                name="grid-animation"
                tag="div"
                class="grid gap-10 md:grid-cols-2 lg:grid-cols-4"
            >
                <NavLink
                    v-for="band in filteredBands"
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
                v-if="filteredBands.length === 0"
                class="py-20 text-center text-gray"
            >
                <p class="text-xl opacity-50">No bands found for this genre.</p>
                <button
                    @click="selectGenre({ id: null, slug: null })"
                    class="mt-4 text-xs font-bold uppercase tracking-widest text-orange hover:underline"
                >
                    Show all bands
                </button>
            </div>
        </div>

        <div class="mt-10 text-pretty border-t border-zinc-800 pt-10 text-gray">
            <p>
                Each band page on <strong>Rocker.am</strong> photos, videos, and
                information about upcoming concerts. Stay connected with the
                Armenian rock scene and follow your favorite groups.
            </p>
        </div>
    </GuestLayout>
</template>

<style scoped>
/*
  NEW ANIMATION: Smooth 3D Flip & Fade
*/
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
