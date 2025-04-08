<script setup>
import ProfileActions from '@/Components/Profile/ProfileActions.vue';
import QrGenerate from '@/Components/Profile/QrGenerate.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

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
    <GuestLayout>
        <!--        <template #header>-->
        <!--            <h2-->
        <!--                class="text-xl font-semibold leading-tight text-gray-800"-->
        <!--            >-->
        <!--                Profile-->
        <!--            </h2>-->
        <!--        </template>-->

        <div>
            <div
                class="relative mx-auto mt-28 bg-graydark py-2 sm:px-6 lg:px-8"
            >
                <div
                    class="absolute left-1/2 top-[-100px] w-fit -translate-x-1/2 text-center"
                >
                    <div class="relative mx-auto w-fit">
                        <img
                            src="/images/user.avif"
                            alt="Profile Picture"
                            class="mx-auto mt-6 h-32 w-32 rounded-full border-4 border-white object-cover"
                        />
                        <QrGenerate v-if="url" :url />
                    </div>
                    <div class="text-gray-900 p-6 font-bold">
                        {{ user.name }}
                    </div>
                </div>
                <div class="mx-auto mt-32 w-2/6 text-center">
                    <small class="block text-sm text-gray">
                        "Creative professional based in Kyiv, passionate about
                        building meaningful projects and bringing ideas to life.
                        Focused on quality, collaboration, and continuous
                        growth."
                    </small>
                    <div class="mt-5 flex flex-col">
                        <a href="mailto:example@gmail.com">example@gmail.com</a>
                        <a
                            href="https://www.facebook.com/groups/286409629705239/"
                            >facebook.com/groups/286409629705239/</a
                        >
                    </div>
                </div>
                <ProfileActions v-if="owner" class="mx-auto w-1/2" />
            </div>
        </div>
    </GuestLayout>
</template>
