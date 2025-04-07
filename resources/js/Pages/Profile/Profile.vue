<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProfileActions from '@/Components/Profile/ProfileActions.vue';
import QrGenerate from '@/Components/Profile/QrGenerate.vue';

defineProps({
    user: {
        type: Object,
        required: true,
    },
    owner: {
        type: Boolean,
        required: true,
    },
    url: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Profile" />

    <ProfileLayout>
        <!--        <template #header>-->
        <!--            <h2-->
        <!--                class="text-xl font-semibold leading-tight text-gray-800"-->
        <!--            >-->
        <!--                Profile-->
        <!--            </h2>-->
        <!--        </template>-->

        <div>
            <div class="mx-auto bg-graydark py-2 sm:px-6 lg:px-8">
                <div class="mx-auto w-fit text-center">
                    <div class="relative">
                        <img
                            src="/images/user.avif"
                            alt="Profile Picture"
                            class="mx-auto mt-6 h-32 w-32 rounded-full object-cover"
                        />
                        <QrGenerate v-if="url" :url />
                    </div>
                    <div class="text-gray-900 p-6">
                        {{ user.name }}
                    </div>
                </div>
                <ProfileActions v-if="owner" class="mx-auto w-1/2" />
            </div>
        </div>
    </ProfileLayout>
</template>
