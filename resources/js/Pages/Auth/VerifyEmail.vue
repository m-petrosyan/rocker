<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import { onMounted } from 'vue';

const props = defineProps({
    status: {
        type: String
    }
});

const form = useForm({
    code: ''
});

const submit = () => {
    form.post(route('verification.send'));
};

onMounted(() => {
    // submit();
});

const verify = async () => {
    form.post(route('verification.verify'));
};
</script>

<template>
    <GuestLayout :meta="{title:'Email Verification'}">
        <div class="w-1/3 mx-auto bg-graydark p-6 rounded-lg">
            <ErrorMessages :messages="$page.props.errors" />
            <h3 class="mb-4 text-md text-gray-600">
                Thanks for signing up! Before getting started, could you verify your
                email address by clicking on the link we just emailed to you? If you
                didn't receive the email, we will gladly send you another.
            </h3>
            <SuccessMessages :message="status" />
            <form @submit.prevent="verify">
                <TextInput
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.code"
                    required
                    autofocus
                />
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Verify
                </PrimaryButton>
            </form>
            <form @submit.prevent="submit">
                <div class="mt-4 flex items-center justify-between">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Resend Verification Email
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
