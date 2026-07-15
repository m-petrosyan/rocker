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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import FacebookIcon from '@/Components/Icons/FacebookIcon.vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import { router, useForm } from '@inertiajs/vue3';
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
const showFbModal = ref(false);

const fbForm = useForm({
  fb_page_url: ''
});

const openFbModal = () => {
  fbForm.fb_page_url = '';
  showFbModal.value = true;
};

const closeFbModal = () => {
  showFbModal.value = false;
  fbForm.reset();
};

const saveFbSource = () => {
  fbForm.post(route('profile.facebook.source'), {
    onSuccess: () => closeFbModal()
  });
};

const deleteFbPage = (url) => {
  if (!confirm('Disconnect this Facebook page? All imported events will remain on the site.')) return;
  router.delete(route('profile.facebook.source.delete'), {
    data: { fb_page_url: url },
    preserveScroll: true,
  });
};
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

      <ErrorMessages :messages="$page.props.errors" />

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

          <!-- Facebook Sources Connect (up to 3) -->
          <div v-if="owner" class="mt-6 space-y-3">
            <div v-for="fbPage in user.facebook_pages" :key="fbPage.id"
                 class="flex items-center justify-between gap-2 bg-gray-800 bg-opacity-50 rounded-lg px-3 py-2">
              <span class="text-xs text-gray-300 truncate flex-1 text-left">
                {{ fbPage.page_url }}
              </span>
              <button @click="deleteFbPage(fbPage.page_url)"
                      class="text-red-400 hover:text-red-300 text-lg leading-none shrink-0"
                      title="Disconnect">
                &times;
              </button>
            </div>

            <button v-if="user.facebook_pages?.length < 3"
              @click="openFbModal"
              class="flex items-center justify-center gap-2 mx-auto px-4 py-2 text-gray-400 hover:text-white text-sm font-medium rounded-lg transition-colors duration-200"
            >
              <FacebookIcon size="18px" />
              <span>{{ user.facebook_pages?.length ? '➕ Add another page' : 'Connect Facebook page' }}</span>
            </button>
            <p v-if="user.facebook_pages?.length" class="mt-1 text-xs text-gray-500">
              Connected: {{ user.facebook_pages.length }} / 3
            </p>
            <p class="mt-2 text-xs text-gray-500 leading-relaxed">
              New events from connected pages will be automatically imported every day.
            </p>
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

    <!-- Facebook Source Modal -->
    <Modal :show="showFbModal" @close="closeFbModal">
      <div class="p-6 text-white bg-black">
        <h2 class="text-lg font-medium flex items-center gap-2">
          <FacebookIcon size="22px" class="text-blue-500" />
          Connect Facebook page
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Enter the URL of the Facebook page from which the bot will automatically collect new events.
        </p>
        <div class="mt-6">
          <InputLabel for="fb_page_url" value="Facebook Page URL" class="text-white" />
          <TextInput
            id="fb_page_url"
            v-model="fbForm.fb_page_url"
            type="url"
            class="mt-1 block w-full bg-gray-800 text-white border-gray-700"
            placeholder="https://www.facebook.com/YourPage"
          />
          <InputError :message="fbForm.errors.fb_page_url" class="mt-2" />
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <SecondaryButton @click="closeFbModal">Cancel</SecondaryButton>
          <PrimaryButton
            class="bg-blue-600 hover:bg-blue-700"
            :class="{ 'opacity-25': fbForm.processing }"
            :disabled="fbForm.processing"
            @click="saveFbSource"
          >
            <FacebookIcon size="16px" class="inline mr-1" />
            {{ fbForm.processing ? 'Saving...' : 'Connect' }}
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </ProfileLayout>
</template>
