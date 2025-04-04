<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';

const props = defineProps({
    status: {
        type: String
    }
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

submit();

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent'
);
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

            <div
                class="mb-4 text-sm font-medium text-green-600"
                v-if="verificationLinkSent"
            >
                A new verification link has been sent to the email address you
                provided during registration.
            </div>

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
