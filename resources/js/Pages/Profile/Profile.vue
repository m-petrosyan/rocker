<script setup>
import ProfileActions from '@/Components/Profile/ProfileActions.vue';
import QrGenerate from '@/Components/Profile/QrGenerate.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';

defineProps({
    user: {
        type: Object,
        required: true
    },
    owner: {
        type: Boolean,
        required: true
    },
    url: {
        type: String,
        required: true
    }
});
</script>

<template>
    <ProfileLayout :meta="{title: 'Profile'}">
        <!--        <template #header>-->
        <!--            <h2-->
        <!--                class="text-xl font-semibold leading-tight text-gray-800"-->
        <!--            >-->
        <!--                Profile-->
        <!--            </h2>-->
        <!--        </template>-->


        <div
            class="relative">
            <div class="absolute left-1/2 top-[-220px] w-fit -translate-x-1/2 text-center">
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
            <SuccessMessages class="w-1/3 mx-auto" :message="$page.props.flash.success" timeout="10000" />
            <div class="mx-auto mt-32 p-2 w-full md:w-2/6 text-center">
                <small class="block text-sm text-gray">
                    "Creative professional based in Kyiv, passionate about
                    building meaningful projects and bringing ideas to life.
                    Focused on quality, collaboration, and continuous
                    growth."
                </small>
                <div class="mt-5 flex flex-col">
                    <a href="mailto:example@gmail.com">example@gmail.com</a>
                    <a href="https://www.facebook.com/groups/286409629705239/">
                        facebook.com/groups/286409629705239/
                    </a>
                </div>
            </div>
            <ProfileActions v-if="owner" class="mx-auto w-full" />
        </div>
    </ProfileLayout>
</template>
