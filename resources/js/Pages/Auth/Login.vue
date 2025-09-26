<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import AuthLayouth from '@/Layouts/AuthLayouth.vue';
import GoogleLogin from '@/Components/Forms/GoogleLogin.vue';
import { webApp } from '@/Helpers/setAppUser.js';
import Preloader from '@/Components/Icons/Preloader.vue';

defineProps({
    canResetPassword: {
        type: Boolean
    },
    status: {
        type: String
    }
});

const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password')
    });
};

const isWebApp = webApp();
</script>

<template>
    <AuthLayouth title="Log in">
        <template v-if="!isWebApp">
            <div v-if="status" class="text-green-600 mb-4 text-sm font-medium">
                {{ status }}
            </div>
            <GoogleLogin />
            <form @submit.prevent="submit" class="mt-5">
                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="text-gray-600 ms-2 text-sm">Remember me</span>
                    </label>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-gray-600 hover:text-gray-900 rounded-md text-sm underline focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Forgot your password?
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </template>
        <Preloader v-else />
    </AuthLayouth>
</template>
