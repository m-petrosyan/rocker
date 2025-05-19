<script setup>
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import UserInfo from '@/Components/Profile/UserInfo.vue';
import Logout from '@/Components/Profile/Logout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: false
    },
    owner: {
        type: Boolean,
        required: true
    }
});

const form = useForm(
    {
        ...props.user,
        bio: '',
        links: [
            { url: '' },
            { url: '' },
            { url: '' }
        ]
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
            <Logout owner />
            <UserInfo :user :owner />
            <div class="mt-48">
                <div class="flex flex-col-reverse md:flex-row gap-4">
                    <textarea
                        class="w-3/6 mx-auto"
                        type="text"
                        rows="5"
                        v-model="form.info"
                        placeholder="info"
                    />
                    <div class="flex w-3/6 flex-col gap-4">
                        <input
                            v-for="(link,index) in form.links"
                            class="w-full bg-graydark2"
                            type="text"
                            v-model="link.url"
                            placeholder="Url"
                        />
                    </div>
                </div>
                <div class="mt-4 flex flex-col-reverse md:flex-row gap-4">
                    <input
                        type="text"
                        disabled
                        v-model="form.email"
                        placeholder="Email"
                    />
                    <input
                        type="text"
                        v-model="form.username"
                        placeholder="Username"
                    />
                    <input
                        type="text"
                        v-model="form.name"
                        placeholder="Name"
                    />
                    <input
                        type="text"
                        v-model="form.password"
                        placeholder="new password"
                    />
                    <input
                        type="text"
                        v-model="form.password_confirmation"
                        placeholder="confirm password">
                </div>
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
