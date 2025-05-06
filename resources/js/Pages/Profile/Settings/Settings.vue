<script setup>
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: false
    }
});

const form = useForm(
    {
        ...props.user
    }
);

const submitSettings = () => {
    form.put(route('profile.settings.store'), {
        forceFormData: true
    });
};
</script>

<template>
    <ProfileLayout :meta="{title: user.name, image: user?.image?.thumb}">
        <form @submit.prevent="submitSettings" class="px-4 md:px-0">
            <div class="flex flex-col-reverse md:flex-row gap-4">
                <input
                    class="bg-graydark2 w-full mx-auto block"
                    type="text"
                    disabled
                    v-model="form.email"
                    placeholder="Email"
                />
                <input
                    class="bg-graydark2 w-full mx-auto block"
                    type="text"
                    v-model="form.username"
                    placeholder="Username"
                />
                <input
                    class="bg-graydark2 w-full mx-auto block"
                    type="text"
                    v-model="form.name"
                    placeholder="Name"
                />
                <input
                    class="bg-graydark2 w-full mx-auto block"
                    type="text"
                    v-model="form.password"
                    placeholder="new password"
                />
                <input
                    class="bg-graydark2 w-full mx-auto block"
                    type="text"
                    v-model="form.password_confirmation"
                    placeholder="confirm password">
            </div>

            <PrimaryButton
                class="ms-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                {{ 'Update' }}
            </PrimaryButton>
        </form>
    </ProfileLayout>
</template>
