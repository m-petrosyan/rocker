<script setup>
import NavLink from '@/Components/NavLink.vue';
import Modal from '@/Components/Modal/Modal.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';


const props = defineProps({
  event: {
    type: Object,
    required: true
  }
});


const form = useForm({
    reason: '',
    status: null
  }
);

const toggleModal = () => {
  form.reset();
  openModal.value = !openModal.value;
};

const openModal = ref(false);

const submit = (status) => {
  form.status = status;
  
  form.put(route('profile.event.request.update', props.event.id), {
    preserveScroll: true,
    onSuccess: () => openModal.value = false,
    onError: () => console.log('error'),
    onFinish: () => form.reset()
  });
};
</script>

<template>
  <div class="profile">
    <h2 class="mt-5 text-center text-red">This event is pending approval by admin.</h2>
    <div class="flex gap-2 mx-auto w-fit mt-5">
      <button @click="toggleModal" class="rounded bg-red px-4 py-2 text-white hover:bg-red-600">
        Reject
      </button>
      <button @click="submit('accepted')" class="rounded bg-green px-4 py-2 text-white hover:bg-green-600">
        Approve
      </button>
    </div>
    <NavLink class="flex gap-2 items-center bg-grayblue px-2 mt-5 text-white w-fit mx-auto rounded" tooltip="Edit"
             :href="route('profile.events.edit', event.id)">
      Edit
      <EditIcon />
    </NavLink>
    <Modal :show="openModal" :close="toggleModal">
        <textarea
          v-model="form.reason"
          placeholder="Enter reason for rejection..."
          class="w-full px-3 py-2 mb-4 "
          rows="4"
        />

      <div class="flex gap-3 justify-end">
        <button
          @click="toggleModal"
          class="px-4 py-2 rounded bg-gray"
        >
          Cancel
        </button>
        <button
          @click="submit('rejected')"
          class="px-4 py-2 rounded bg-red text-white hover:bg-red-600"
        >
          Submit
        </button>
      </div>
    </Modal>
  </div>
</template>