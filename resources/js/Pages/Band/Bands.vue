<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';
import NavLink from '@/Components/NavLink.vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  bands: {
    type: Object
  },
  genres: {
    type: Array,
    default: () => []
  },
  user: {
    type: Boolean,
    default: false
  },
  add: {
    type: Boolean,
    default: false
  }
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
watch(() => page.url, () => {
    syncGenreFromUrl();
});

const filteredBands = computed(() => {
  if (!selectedGenreSlug.value) return props.bands.data;
  return props.bands.data.filter(band =>
    band.genres.some(genre => 
        genre.slug === selectedGenreSlug.value || 
        genre.id.toString() === selectedGenreSlug.value
    )
  );
});

const selectGenre = (genre) => {
  // Если передали null или объект с id: null (сброс фильтра)
  if (!genre || genre.id === null) {
    selectedGenreSlug.value = null;
  } else {
    const identifier = genre.slug || genre.id.toString();
    selectedGenreSlug.value = selectedGenreSlug.value === identifier ? null : identifier;
  }

  // Обновляем URL
  const url = new URL(window.location.href);
  if (selectedGenreSlug.value) {
    url.searchParams.set('genre', selectedGenreSlug.value);
  } else {
    url.searchParams.delete('genre');
  }
  
  // Используем Inertia router для обновления URL
  router.get(url.pathname, { genre: selectedGenreSlug.value || undefined }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
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
        description: 'Discover Armenian rock and metal bands. Explore profiles, genres, photos, and stories from the Yerevan rock and metal scene on Rocker.am.',
        keywords: 'armenian rock bands, armenian metal bands, yerevan rock scene, armenian hard rock, armenian heavy metal, armenian punk, armenian alternative rock, armenian folk metal, armenian thrash metal, armenian progressive rock, armenian doom metal, armenian post-rock, armenian gothic rock, armenian underground music' + ', ' + bands.data.map(band => band.name).join(', ')
    }">
    <template #header> Bands</template>
    <div class="text-gray text-pretty">
      <h2 class="text-lg font-medium text-gray mb-6 text-orange/80">Armenian Rock & Metal Bands</h2>
      <p>Discover the best Armenian rock and metal bands on <strong>Rocker.am</strong>. From legendary pioneers of the Yerevan scene to emerging artists, explore the musicians shaping Armenia’s rock and metal sound.</p>
    </div>

    <!-- Genre Filter -->
    <div class="mt-8 flex flex-wrap gap-2">
        <button
            @click="selectGenre({ id: null, slug: null })"
            class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-300 border"
            :class="!selectedGenreSlug ? 'bg-orange border-orange text-white' : 'bg-graydark2 border-graydark2 text-gray hover:border-gray'"
        >
            All Genres
        </button>
        <button
            v-for="genre in genres"
            :key="genre.id"
            @click="selectGenre(genre)"
            class="px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-300 border"
            :class="selectedGenreSlug === (genre.slug || genre.id.toString()) ? 'bg-orange border-orange text-white shadow-lg shadow-orange/20' : 'bg-graydark2 border-graydark2 text-gray hover:border-gray'"
        >
            {{ genre.name }}
        </button>
    </div>

    <template #h1> Discover Armenian rock and metal bands</template>

    <div class="mt-10 relative">
        <TransitionGroup
            name="grid-animation"
            tag="div"
            class="grid gap-10 md:grid-cols-2 lg:grid-cols-4"
        >
            <NavLink
                v-for="band in filteredBands"
                :href="route('bands.show', band.slug)"
                :key="band.id"
                class="flex flex-col items-center group transition-all duration-500"
            >
                <div class="relative aspect-square w-full">
                    <div class="lg:h-[300px] h-full w-full bg-black rounded-xl overflow-hidden shadow-lg transition-all duration-300">
                        <img
                            :src="band.logo?.svg ?? band.logo.thumb"
                            @load="e => onImgLoad(e, band.id)"
                            class="w-full h-full object-center band-img transition-transform duration-500 group-hover:scale-110"
                            :class="wideFlags[band.id] ? 'object-contain' : 'object-cover'"
                            :alt="band.name"
                            @error="$event.target.src = band.logo.original"
                        />
                    </div>
                </div>
                <div class="p-2 text-center mt-2">
                    <p class="text-lg font-semibold text-pretty">
                        {{ band.name.length > 40 ? band.name.slice(0, 40) + '...' : band.name }}
                    </p>

                </div>
            </NavLink>

            <NavLink
                v-if="add && !selectedGenreId"
                :href="route('profile.bands.create')"
                key="add-band"
                class="flex min-h-64 items-center gap-2 border-2 border-dashed border-zinc-800 p-4 rounded-xl hover:border-orange hover:bg-zinc-900 transition-all duration-300 group"
            >
                <div class="mx-auto flex w-32 flex-col items-center gap-y-4">
                    <h2 class="text-4xl group-hover:scale-125 transition-transform duration-300 text-zinc-600 group-hover:text-orange">+</h2>
                    <h3 class="text-zinc-600 group-hover:text-orange transition-colors font-bold uppercase tracking-widest text-xs">Add band</h3>
                </div>
            </NavLink>
        </TransitionGroup>

        <div v-if="filteredBands.length === 0" class="text-center py-20 text-gray">
            <p class="text-xl opacity-50">No bands found for this genre.</p>
            <button @click="selectGenre({ id: null, slug: null })" class="mt-4 text-orange font-bold hover:underline uppercase tracking-widest text-xs">Show all bands</button>
        </div>
    </div>

    <div class="text-gray text-pretty mt-10 border-t border-zinc-800 pt-10">
      <p>Each band page on <strong>Rocker.am</strong> photos, videos, and information about upcoming concerts. Stay connected with the Armenian rock scene and follow your favorite groups.</p>
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
  .grid-animation-leave-active { width: calc(50% - 1.25rem); }
}
@media (min-width: 1024px) {
  .grid-animation-leave-active { width: calc(25% - 1.875rem); }
}
</style>
