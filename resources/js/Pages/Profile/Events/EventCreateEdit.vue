<script setup>
import { reactive } from 'vue';
import RadioSwichButton from '@/Components/Forms/RadioSwichButton.vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import { useForm } from '@inertiajs/vue3';
import DatePicker from '@/Components/Forms/DatePicker.vue';
import Preview from '@/Components/Forms/Preview.vue';
import GoogleAutocomplate from '@/Components/Maps/GoogleAutocomplate.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    event: {
        type: Object,
        required: false
    },
    role: {
        type: String,
        required: true
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
            price: null
        }
);

const createEvent = () => {
    // data.disable = true;
    form.post(
        route(
            props.event?.id ? 'profile.events.update' : 'profile.events.store',
            props.event?.id
        ),
        {
            onSuccess: () => {
                // data.created = true;
            },
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
    <GuestLayout title="Event create" :role="role">
        <div
            v-if="!data.created"
            class="mx-auto bg-graydark py-2 sm:px-6 lg:px-8"
        >
            <ErrorMessages :messages="$page.props.errors" class="mb-5" />
            <form @submit.prevent="createEvent" class="flex flex-col gap-y-2">
                <div class="flex gap-x-4">
                    <div class="w-1/2">
                        <Preview
                            class="h-full"
                            label="h-full"
                            :image="form.poster"
                            v-model:preview="form.poster_file"
                            v-model:file="data.preview"
                        />
                    </div>
                    <div class="flex w-1/2 flex-col gap-y-2">
                        <div>
                            <input
                                class="w-full bg-graydark2"
                                type="text"
                                v-model="form.title"
                                placeholder="Title"
                                tabindex="1"
                                enterkeyhint="next"
                            />
                        </div>
                        <RadioSwichButton
                            v-model:selectedOption="form.type"
                            :options="types"
                        />
                        <RadioSwichButton
                            v-model:selectedOption="form.genre"
                            :options="genres"
                        />
                        <GoogleAutocomplate :form="form" />
                        <DatePicker
                            v-model:start_date="form.start_date"
                            v-model:start_time="form.start_time"
                        />
                    </div>
                </div>
                <div class="relative mt-2">
                    <textarea
                        class="w-full bg-graydark2"
                        type="text"
                        rows="10"
                        v-model="form.content"
                        placeholder="Content"
                        tabindex="2"
                        enterkeyhint="next"
                    >
                    </textarea>
                    <span
                        class="text-md absolute right-0 top-0 px-1"
                        :class="
                            form.content?.length >= 730
                                ? 'bg-red text-white'
                                : 'text-gray'
                        "
                    >
                        {{ form.content?.length }}
                    </span>
                </div>

                <div class="flex gap-x-2">
                    <div class="w-1/2">
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
                    <div class="w-1/2">
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
        <div v-else class="flex h-dvh flex-col items-center justify-center">
            <h2 class="text-balance">
                Thank you, the event has been created.
                <span v-if="role === 'USER'">
                    The event will be added to the list after moderation</span
                >
            </h2>
            <!--            <YellowButton text="Events list" navigate="events.index" />-->
        </div>
    </GuestLayout>
</template>
