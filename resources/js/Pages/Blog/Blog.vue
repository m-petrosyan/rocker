<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BandTags from '@/Components/Tags/BandTags.vue';
import NavLink from '@/Components/NavLink.vue';
import { ref } from 'vue';
import { getUrlQuery } from '@/Helpers/urlHelper.js';
import SocialShare from '@/Components/Socials/SocialShare.vue';

const props = defineProps({
    blog: {
        type: Object
    },
    url: {
        type: String
    }
});

const lang = ref(getUrlQuery('lang') ?? 'en');

const setLang = (language) => {
    lang.value = language;
    props.url.searchParams.set('lang', language);
    if (typeof window !== 'undefined') {
        window.history.pushState({}, '', props.url);
    }
};
</script>

<template>
    <GuestLayout
        :meta="{title:  blog.title['en'] ?? blog.title['am']  ,image:blog.cover.large , description: blog.description['en'] ?? blog.description['am'] , author: blog.author ?? blog.user.name,keywords: blog.bands.map(band => band?.name).join(',')}">
        <h1 class="text-center">{{ blog.title[lang] }}</h1>
        <div class="flex flex-col-reverse md:flex-row mt-5 gap-x-4 gap-y-6">
            <div class="md:w-2/3">
                <div
                    :style="{ backgroundImage: `url(${blog.cover.large})` }"
                    class="relative h-96 bg-cover bg-center"
                >
                    <div class="flex justify-between gap-2 absolute bottom-0 w-full bg-black bg-opacity-20 p-4">
                        <div>
                            <button v-if="blog.title['en']" @click="setLang('en')">ENG</button>
                            <button v-if="blog.title['am']" @click="setLang('am')">ARM</button>
                        </div>
                        <SocialShare class="absolute right-0 bottom-0 bg-opacity-20 bg-graydark"
                                     :title="blog.title['en'] ?? blog.title['am']"
                                     :url />
                    </div>
                </div>
            </div>
            <div class="md:w-1/3 flex flex-col justify-between gap-y-4 bg-graydark2 p-4 rounded-lg text-pretty">
                <p>{{ blog.description[lang] }}</p>
                <BandTags :bands="blog.bands" />
            </div>
        </div>
        <div class="flex justify-center mt-10">
            <NavLink v-if="!blog.author" :href="route('profile.show', blog.user.username)"
                     class="text-center flex flex-col items-center gap-y-4 p-2">
                <div class="h-16 w-16 rounded-full overflow-hidden">
                    <img :src="blog.user?.image?.thumb ?? '/images/user.jpg'" class="object-contain"
                         alt="">
                </div>
                <p>{{ blog.user.name }}</p>
            </NavLink>
            <div v-else>
                author : {{ blog.author }}
            </div>
        </div>
        <div class="md:w-5/6 mx-auto mt-8 md:p-0 p-3" v-html="blog.content[lang]" />
    </GuestLayout>
</template>
