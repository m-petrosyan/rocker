<script setup>
import BotIcon from '@/Components/Icons/BotIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import { ref } from 'vue';
import MergeIcon from '@/Components/Icons/MergeIcon.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
  user: {
    type: Object,
    required: true
  }
});

const mergeUsers = ref(false);


const form = useForm(
  {
    code: ''
  }
);


const createCode = () => {
  form.post(route('profile.merge.code'),
    {
      onError: () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      },
      preserveScroll: true
    }
  );
};

const mergeAccounts = () => {
  form.post(route('profile.merge.bot'),
    {
      onError: () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      },
      preserveScroll: true
    }
  );
};
</script>

<template>
  <div class="mx-auto w-fit mt-20">
    <div v-if="!user.chat  || !user.email" class="text-center">
      <button @click.prevent="mergeUsers = !mergeUsers" tooltip="Merge accounts" class="flex mx-auto">
        <UserIcon />
        <MergeIcon />
        <BotIcon />
      </button>
      <div v-if="mergeUsers" class="mt-4">
        <div v-if="user.chat">
          <h4>Merge users</h4>
          <small>If you have an account on rocker.am, you can merge your profiles.
            To do this, click "Request Code", then go to rocker.am → Profile → Settings and enter the
            code there.</small>
          <br />
          <div v-if="user.merge_code?.code">
            <h3 class="p-2 bg-graydark2 w-fit mx-auto mt-4 rounded-lg">{{ user.merge_code.code }}</h3>
          </div>
          <button v-else @click.prevent="createCode" class="bg-green w-fit p-2 mx-auto mt-4">Request code</button>
        </div>
        <div v-else>
          <div v-if="mergeUsers">
            <small>If you are using Rocker Bot in Telegram, you can merge your accounts.
              To do this, open your Telegram → Rocker bot → Profile → Settings, click "Request Code", and enter
              the
              code here.</small><br />
            <input v-model="form.code" type="text" placeholder="code" class="mt-4">
            <button @click.prevent="mergeAccounts" class="bg-red w-fit p-2 mx-auto">Merge</button>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <small>Your accounts have already been merged.</small>
    </div>
  </div>
</template>