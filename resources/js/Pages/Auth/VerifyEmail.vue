<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import { onMounted } from 'vue';
import { getUrlQuery } from '@/Helpers/urlHelper.js';
import AuthLayouth from '@/Layouts/AuthLayouth.vue';

defineProps({
    status: {
        type: String
    }
});

const form = useForm({
    code: getUrlQuery('code')
});

const submit = () => {
    form.post(route('verification.send'));
};

onMounted(() => {
    if (form.code) {
        verify();
    } else {
        // submit();
    }
});

const verify = async () => {
    form.post(route('verification.verify'));
};
</script>

<template>
    <AuthLayouth title="Email Verification">
        <ErrorMessages :messages="$page.props.errors" />
        <h4 class="text-md  mb-4">
            Thanks for signing up! Before getting started, could you verify
            your email address by clicking on the link we just emailed to
            you? If you didn't receive the email, we will gladly send you
            another.
        </h4>
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
    </AuthLayouth>
</template>
