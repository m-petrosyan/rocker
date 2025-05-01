<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import UserInfo from '@/Components/Profile/UserInfo.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import LogoutIcon from '@/Components/Icons/LogoutIcon.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';
import GalleryWrapper from '@/Components/Wrappers/GalleryWrapper.vue';
import ProfileActions from '@/Components/Profile/ProfileActions.vue';
import EventWrapper from '@/Components/Wrappers/EventWrapper.vue';
import BandWrapper from '@/Components/Wrappers/BandWrapper.vue';

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
            <div v-if="owner" class="absolute right-0 top-0">
                <ResponsiveNavLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                >
                    <LogoutIcon />
                </ResponsiveNavLink>
            </div>
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
            <div class="mt-48">
                <SuccessMessages success class="w-1/3 mx-auto" :message="$page.props.flash.success" timeout="10000" />
                <ProfileActions v-if="owner" class="mx-auto w-full" />
                <GalleryWrapper :galleries="galleries" :owner :isAdmin="auth.isAdmin" title="User galleries" />
                <EventWrapper :events :owner :isAdmin="auth.isAdmin" title="User events" />
                <BandWrapper v-if="owner" :bands :owner :isAdmin="auth.isAdmin" title="User bands" />
            </div>
        </div>
    </ProfileLayout>
</template>
