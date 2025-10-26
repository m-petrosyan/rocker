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
import { isWebApp } from '@/Helpers/setAppUser.js';
import EventIcon from '@/Components/Icons/EventIcon.vue';

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
  eventRequests: {
    type: [Number, null],
    required: false,
    default: false
  },
  auth: {
    object: true
  }
});

const webApp = isWebApp();
</script>

<template>
  <ProfileLayout :meta="{title: user.name, image: user?.image?.thumb}">
    <div>
      <UserInfo :user :owner />
      <div class="flex justify-between">
        <NavLink v-if="['admin','modarator','organizer'].includes(auth.role) && owner"
                 :href="route('profile.dashboard')"
                 class=" flex bg-black bg-opacity-20">
          <div tooltip="Dashboard">
            <AnalyticsIcon class="text-white" />
          </div>
        </NavLink>
        <span v-else />
        <div class="flex flex-col items-end gap-y-4">
          <NavLink v-if="eventRequests"
                   :href="route('profile.events.requests')"
                   class="relative">
            <div tooltip="Event Requests">
              <EventIcon class="text-white" />
              <span
                class="absolute -top-2 -left-2 bg-orange text-white text-xs font-semibold w-6 h-6 flex items-center justify-center rounded-full">
                {{ eventRequests }}
              </span>
            </div>
          </NavLink>
          <Logout v-if="!webApp" :owner />
        </div>
      </div>
      <div class="mx-auto mt-20 p-2 w-full md:w-2/6 text-center">
        <h3 class="text-gray-900 p-6">
          {{ user.name }}
        </h3>
        <small class="block text-sm text-gray">
          {{ user.info }}
        </small>
        <div class="w-2/3 mx-auto">
          <div v-if="user.links.length"
               class="flex items-center md:flex-col gap-x-5 font-bold text-gray p-3">
            <a v-for="link in user.links" :key="link.id" :href="link.url"
               target="_blank">{{ getHostname(link.url)
              }}</a>
          </div>
        </div>
      </div>
      <div class="mt-56 md:mt-48">
        <SuccessMessages success class="w-full md:w-1/3 mx-auto" :message="$page.props.flash.success" timeout="10000" />
        <ProfileActions v-if="owner" class="mx-auto w-full" :full="auth.user.settings?.country !== 'ge'" />
        <GalleryWrapper v-if="galleries.data?.length" profile :galleries="galleries.data" :owner
                        :isAdmin="auth.isAdmin"
                        title="User galleries" />
        <EventWrapper
          v-if="events?.data?.length && (owner || ['admin','modarator','organizer'].includes(auth.role))"
          :events="events?.data" :owner
          profile
          :isAdmin="auth.isAdmin" title="User events" />
        <BandWrapper
          v-if="bands.data?.length && (owner || ['admin','modarator','organizer'].includes(auth.role))"
          :bands="bands.data" :owner
          :isAdmin="auth.isAdmin"
          title="User bands" />
        <BlogWrapper
          v-if="blogs.data?.length && (owner || ['admin','modarator','organizer'].includes(auth.role))"
          :blogs="blogs.data" :owner
          :isAdmin="auth.isAdmin"
          title="User blogs" blogs="" />
      </div>
    </div>
  </ProfileLayout>
</template>
