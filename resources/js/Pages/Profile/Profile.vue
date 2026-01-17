<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import UserInfo from '@/Components/Profile/UserInfo.vue';
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
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';


const props = defineProps({
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

const showBlockModal = ref(false);
const blockForm = useForm({
  reason: ''
});

const closeBlockModal = () => {
  showBlockModal.value = false;
  blockForm.reset();
};

const blockUser = () => {
  blockForm.post(route('profile.user.block', props.user.id), {
    onSuccess: () => closeBlockModal()
  });
};

</script>

<template>
  <ProfileLayout :meta="{title: user.name, image: user?.image?.thumb}">
    <div>
      <div v-if="user.is_blocked" class="bg-red-600 text-white text-center p-4 font-bold text-xl rounded mb-4">
        BLOCKED
      </div>

      <div v-if="['admin', 'moderator'].includes($page.props.auth.user?.role) && !owner && !user.is_blocked"
           class="flex justify-end px-4">
        <DangerButton @click="showBlockModal = true">
          Block User
        </DangerButton>
      </div>

      <UserInfo :user :owner />
      <div class="flex justify-between">
        <NavLink v-if="['admin','moderator','organizer'].includes(auth.role) && owner"
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
      <div class="mx-auto mt-10 p-2 w-full md:w-2/6 text-center">
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
      <div v-if="user.is_blocked && ['admin','moderator','organizer'].includes(auth.role)">
        <div class="mx-auto text-center w-fit">
          <b class="bg-red text-white p-1">Blocked</b>
          <p class="mt-5 border border-dashed border-red" v-html="'Reason: '+user.blocked_record?.reason " />
        </div>
      </div>
      <div class="mt-20">
        <ProfileActions v-if="owner && !user.is_blocked" class="mx-auto w-full"
                        :full="auth.user.settings?.country !== 'ge'" />
        <EventWrapper
          v-if="events?.data?.length && (owner || ['admin','moderator','organizer'].includes(auth.role))"
          :events="events?.data" :owner
          profile
          :isAdmin="auth.isAdmin" title="User events"
          class="mt-20" />
        <GalleryWrapper v-if="galleries.data?.length" profile :galleries="galleries.data" :owner
                        :isAdmin="auth.isAdmin"
                        title="User galleries" />
        <BandWrapper
          v-if="bands.data?.length && (owner || ['admin','moderator','organizer'].includes(auth.role))"
          :bands="bands.data" :owner
          :isAdmin="auth.isAdmin"
          title="User bands" />
        <BlogWrapper
          v-if="blogs.data?.length && (owner || ['admin','moderator','organizer'].includes(auth.role))"
          :blogs="blogs.data" :owner
          :isAdmin="auth.isAdmin"
          title="User blogs" blogs="" />
      </div>
    </div>

    <Modal :show="showBlockModal" @close="closeBlockModal">
      <div class="p-6 text-white bg-black">
        <h2 class="text-lg font-medium">
          Block User
        </h2>
        <p class="mt-1 text-sm text-gray-400">
          Are you sure you want to block this user? They will not be able to login or create content.
        </p>
        <div class="mt-6">
          <InputLabel for="reason" value="Reason" class="text-white" />
          <TextInput
            id="reason"
            v-model="blockForm.reason"
            type="text"
            class="mt-1 block w-full bg-gray-800 text-white border-gray-700"
            placeholder="Reason for blocking"
          />
          <InputError :message="blockForm.errors.reason" class="mt-2" />
        </div>
        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeBlockModal"> Cancel</SecondaryButton>
          <DangerButton
            class="ml-3"
            :class="{ 'opacity-25': blockForm.processing }"
            :disabled="blockForm.processing"
            @click="blockUser"
          >
            Block User
          </DangerButton>
        </div>
      </div>
    </Modal>
  </ProfileLayout>
</template>
