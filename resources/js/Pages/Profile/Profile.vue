<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import UserInfo from '@/Components/Profile/UserInfo.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import ProfileActions from '@/Components/Profile/ProfileActions.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';
import Logout from '@/Components/Profile/Logout.vue';
import BlogWrapper from '@/Components/Wrappers/BlogWrapper.vue';
import NavLink from '@/Components/NavLink.vue';
import AnalyticsIcon from '@/Components/Icons/AnalyticsIcon.vue';
import { getHostname } from '@/Helpers/urlHelper.js';

defineProps({
    user: {
        type: Object,
        required: true
    },
    galleries: {
        type: Object,
        required: false
    },
    events: {
        type: Object,
        required: false
    },
    bands: {
        type: Object,
        required: false
    },
    blogs: {
        type: Object,
        required: false
    },
    owner: {
        type: Boolean,
        required: true
    },
    auth: {
        object: true
    },
    url: {
        type: String,
        required: true
    }
});
</script>

<template>
    <ProfileLayout :meta="{title: user.name, image: user?.image?.thumb}">
        <div>
            <UserInfo :url="url" :user :owner />
            <Logout :owner />
            <NavLink v-if="(auth.isAdmin || auth.isModerator) && owner"
                     :href="route('profile.dashboard')"
                     class="absolute top-0 left-0 z-20 flex bg-black bg-opacity-20">
                <AnalyticsIcon class="h-6 w-6 text-white" />
            </NavLink>
            <!--            <div class="mx-auto mt-32 p-2 w-full md:w-2/6 text-center">-->
            <!--                <small class="block text-sm text-gray">-->
            <!--                    "Creative professional based in Kyiv, passionate about-->
            <!--                    building meaningful projects and bringing ideas to life.-->
            <!--                    Focused on quality, collaboration, and continuous-->
            <!--                    growth."-->
            <!--                </small>-->
            <!--                <div class="mt-5 flex flex-col">-->
            <!--                    <a href="mailto:example@gmail.com">example@gmail.com</a>-->
            <!--                    <a href="https://www.facebook.com/groups/286409629705239/">-->
            <!--                        facebook.com/groups/286409629705239/-->
            <!--                    </a>-->
            <!--                </div>-->
            <!--            </div>-->
            <div class="mt-56 md:mt-48">
                <div class="w-2/3 mx-auto">
                    <p class="text-center text-pretty">{{ user.info }}</p>
                    <div v-if="user.links.length"
                         class="flex items-center md:flex-col gap-x-5 font-bold text-gray p-3">
                        <a v-for="link in user.links" :key="link.id" :href="link.url"
                           target="_blank">{{ getHostname(link.url)
                            }}</a>
                    </div>
                </div>
                <SuccessMessages success class="w-1/3 mx-auto" :message="$page.props.flash.success" timeout="10000" />
                <ProfileActions v-if="owner" class="mx-auto w-full" />
                <GalleryWrapper v-if="galleries.data?.length" :galleries="galleries.data" :owner :isAdmin="auth.isAdmin"
                                title="User galleries" />
                <EventWrapper v-if="events.data?.length && (owner || auth.isAdmin)" :events :owner
                              :isAdmin="auth.isAdmin" title="User events" />
                <BandWrapper v-if="bands.data?.length && (owner || auth.isAdmin)" :bands="bands.data" :owner
                             :isAdmin="auth.isAdmin"
                             title="User bands" />
                <BlogWrapper v-if="blogs.data?.length &&(owner || auth.isAdmin)" :blogs="blogs.data" :owner
                             :isAdmin="auth.isAdmin"
                             title="User blogs" blogs="" />
            </div>
        </div>
    </ProfileLayout>
</template>
