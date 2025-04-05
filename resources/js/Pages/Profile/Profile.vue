<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head } from '@inertiajs/vue3';
import ProfileActions from '@/Components/Profile/ProfileActions.vue';
import QrGenerate from '@/Components/Profile/QrGenerate.vue';

defineProps({
    user: {
        type: Object,
        required: true
    },
    owner: {
        type: Boolean,
        required: true
    }
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
                <div class="text-center w-fit mx-auto">
                    <div class="relative">
                        <img
                            src="/images/user.avif"
                            alt="Profile Picture"
                            class="w-32 h-32 rounded-full mx-auto mt-6 object-cover"
                        />
                        <QrGenerate />
                    </div>
                    <div class="p-6 text-gray-900">
                        {{ user.name }}
                    </div>

                </div>
                <ProfileActions v-if="owner" class="w-1/2 mx-auto" />
            </div>
        </div>
    </ProfileLayout>
</template>
