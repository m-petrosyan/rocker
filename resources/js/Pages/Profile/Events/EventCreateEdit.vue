<script setup>
import { reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RadioSwichButton from '@/Components/Forms/RadioSwichButton.vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';
import { useForm } from '@inertiajs/vue3';
import DatePicker from '@/Components/Forms/DatePicker.vue';
import Preview from '@/Components/Forms/Preview.vue';
import GoogleAutocomplate from '@/Components/Map/GoogleAutocomplate.vue';


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
    { 'name': 'ROCK', 'key': 'rock' },
    { 'name': 'METAL', 'key': 'metal' },
    { 'name': 'MIX', 'key': 'all' }
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
    props.event ? { ...props.event } : {
        title: '',
        content: '',
        country: 'am',
        genre: null,
        type: null,
        location: null,
        price: null,
        cordinates: null,
        start_date: null,
        start_time: null,
        poster: null
    });

const createEvent = () => {
    data.disable = true;
    optionsSet();
    form.post(route(form.id ? 'event.update' : 'event.store', form), {
        onSuccess: () => {
            data.created = true;
        },
        onError: () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            data.disable = false;
        },
        preserveScroll: true
    });
};

const optionsSet = () => {
    if (!data.options.price) {
        form.price = form.ticket = null;
    }
    if (!data.options.link) {
        form.link = null;
    }
};
</script>

<template>
    <AuthenticatedLayout title="Event create" :role="role">
        <div v-if="!data.created" class="mx-auto bg-graydark py-2 sm:px-6 lg:px-8">
            <ErrorMessages :messages="$page.props.errors" />
            <form @submit.prevent="createEvent" class="flex flex-col gap-y-2">
                <Preview
                    class="h-96"
                    label="h-full"
                    :image="form.poster"
                    v-model:preview="form.previewFile"
                    v-model:file="data.preview" />
                <RadioSwichButton v-model:selectedOption="form.type" :options="types" />
                <div>
                    <input class="w-full" type="text" v-model="form.title" placeholder="Title"
                           tabindex="1"
                           enterkeyhint="next">
                </div>
                <div class="relative">
                            <textarea class="w-full" type="text" rows="10" v-model="form.content" placeholder="Content"
                                      tabindex="2"
                                      enterkeyhint="next">
                            </textarea>
                    <span class="absolute top-0 right-4 text-md"
                          :class="form.content?.length >= 730 ? 'text-red' : 'text-graydark'">
                        {{ form.content?.length }}
                    </span>
                </div>
                <RadioSwichButton v-model:selectedOption="form.genre" :options="genres" />
                <GoogleAutocomplate :form="form" />
                <DatePicker v-model:start_date="form.start_date" v-model:start_time="form.start_time" />
                <div class="options rounded-l-md ">
                    <div>
                        <input class="w-full bg-graydark2 border-none" type="text" v-model="form.price"
                               placeholder="price">
                        <input class="w-full bg-graydark2 border-none mt-2" type="url" v-model="form.ticket"
                               placeholder="ticket link">
                    </div>
                    <div class="mt-5">
                        <input id="event" class="w-full mt-2 bg-graydark2 border-none" type="url" v-model="form.link"
                               placeholder="event link">
                    </div>
                </div>
                <br>
                <!--                <YellowButton :disable="data.disable" :text="form.id ? 'Update': 'Create'" />-->
                <!--                <RedButton text="Cancel" navigate="event.index" />-->
                <br>
            </form>
        </div>
        <div v-else class="flex flex-col items-center justify-center h-dvh">
            <h2 class="text-balance">Thank you, the event has been created.
                <span v-if="role === 'USER'"> The event will be added to the list after moderation</span>
            </h2>
            <!--            <YellowButton text="Events list" navigate="events.index" />-->
        </div>
    </AuthenticatedLayout>
</template>
