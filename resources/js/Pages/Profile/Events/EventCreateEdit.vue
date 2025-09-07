<script setup>
import { reactive } from 'vue';
import RadioSwichButton from '@/Components/Forms/RadioSwichButton.vue';
import { useForm } from '@inertiajs/vue3';
import DatePicker from '@/Components/Forms/DatePicker.vue';
import Preview from '@/Components/Forms/Preview.vue';
import GoogleAutocomplate from '@/Components/Maps/GoogleAutocomplate.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import SuccessMessages from '@/Components/Messages/SuccessMessages.vue';

const props = defineProps({
    event: {
        type: Object,
        required: false
    },
    auth: {
        object: true
    },
    bandsList: {
        type: Array,
        required: false
    }
});

const types = [
    { name: 'CONCERT', key: 2 },
    { name: 'EVENT', key: 3 }
];

const genres = [
    { name: 'ROCK', key: 'rock' },
    { name: 'METAL', key: 'metal' },
    { name: 'MIX', key: 'all' }
];

const countries = [
    { name: 'Armenia', key: 'am' },
    { name: 'Georgia', key: 'ge' }
];

const data = reactive({
    options: {
        price: +!!props.event?.price || +!!props.event?.ticket,
        link: +!!props.event?.link
    },
    preview: null,
    created: false,
    disable: false
});

const form = useForm(
    props.event
        ? { ...props.event }
        : {
            title: '',
            content: '',
            country: 'am',
            genre: null,
            type: null,
            location: null,
            cordinates: null,
            start_date: null,
            start_time: null,
            poster_file: null,
            link: null,
            ticket: null,
            price: null,
            bands: []
        }
);

const createEvent = () => {
    form.post(
        route(
            props.event?.id ? 'profile.events.update' : 'profile.events.store',
            props.event?.id
        ),
        {

            onError: () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            },
            preserveScroll: true
        }
    );
};
</script>

<template>
    <ProfileLayout :meta="{title: 'Event create'}">
        <div>
            <SuccessMessages
                info
                class="mb-5">
                <div class="flex items-center gap-x-2">
                    <p>The event will also be published in the Telegram bot </p>
                    <a href="https://t.me/RockMetalEventsbot" target="_blank">@RockMetalEventsbot</a> |
                    <a href="https://t.me/yerevanmetal" target="_blank">Rock Metal Yerevan</a> |
                    <a href="https://t.me/gyumrimetal" target="_blank">Rock Metal Gyumri</a>
                </div>
            </SuccessMessages>
            <form @submit.prevent="createEvent" class="flex flex-col gap-y-2">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <Preview
                            label="preview"
                            class="h-full min-h-96"
                            labelClass="h-full"
                            :image="form.poster"
                            blur
                            v-model:preview="form.poster_file"
                            v-model:file="data.preview"
                        />
                    </div>
                    <div class="flex w-full md:w-1/2 flex-col gap-y-2">
                        <RadioSwichButton v-if="['admin', 'moderator','organizer'].includes(auth.role)"
                                          v-model:selectedOption="form.country"
                                          :options="countries"
                        />
                        <input
                            type="text"
                            v-model="form.title"
                            placeholder="Title"
                        />
                        <RadioSwichButton
                            v-model:selectedOption="form.type"
                            :options="types"
                        />
                        <RadioSwichButton
                            v-model:selectedOption="form.genre"
                            :options="genres"
                        />
                        <GoogleAutocomplate :form="form" />
                        <!--                        <MultiSelect v-if="form.country === 'am'"-->
                        <!--                                     v-tooltip="'If the band is registered on rocker.am, the event will be visible on their page.'"-->
                        <!--                                     v-model="form.bands"-->
                        <!--                                     :options="bandsList"-->
                        <!--                                     text="Bands"-->
                        <!--                                     multiple />-->
                        <DatePicker
                            v-model:start_date="form.start_date"
                            v-model:start_time="form.start_time"
                        />
                    </div>
                </div>
                <div class="relative mt-2">
                    <textarea
                        class="w-full"
                        type="text"
                        rows="10"
                        v-model="form.content"
                        placeholder="Content"
                        tabindex="2"
                        enterkeyhint="next"
                    />

                    <span
                        class="text-md absolute right-0 top-0 px-1"
                        :class="
                            form.content?.length >= 730
                                ? 'bg-red text-white'
                                : 'text-gray'">
                        {{ form.content?.length }}
                    </span>
                </div>

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="w-full md:w-1/2">
                        <input
                            class="w-full bg-graydark2"
                            type="text"
                            v-model="form.price"
                            placeholder="price"
                        />
                        <input
                            class="mt-2 w-full bg-graydark2"
                            type="url"
                            v-model="form.ticket"
                            placeholder="ticket link"
                        />
                    </div>
                    <div class="w-full md:w-1/2">
                        <input
                            id="event"
                            class="w-full bg-graydark2"
                            type="url"
                            v-model="form.link"
                            placeholder="event link"
                        />
                    </div>
                </div>
                <br />
                <!--                <YellowButton :disable="data.disable" :text="form.id ? 'Update': 'Create'" />-->
                <!--                <RedButton text="Cancel" navigate="event.index" />-->
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Create
                </PrimaryButton>
            </form>
        </div>
    </ProfileLayout>
</template>
