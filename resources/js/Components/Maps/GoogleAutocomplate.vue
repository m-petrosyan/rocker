<script setup>
import AutoComplete from 'primevue/autocomplete';
import vueDebounce from 'vue-debounce';
import { reactive, ref, watch } from 'vue';
import ErrorMessages from '@/Components/Messages/ErrorMessages.vue';

const vDebounce = vueDebounce({ lock: true });

const props = defineProps({
    form: {
        type: Object,
        required: true
    }
});

const data = reactive({
    places: [],
    filteredCountries: [],
    selectedCountry: props.form.location,
    queryText: null
});

const searchCountries = (text) => {
    data.queryText = text.query;
    return data.places;
};
const warningText = ref('');

const key = import.meta.env.VITE_SERPER_AUTOCOMPLATE_KEYS;

const getPlacesQuery = () => {
    setTimeout(() => {
        setTimeout(() => {
            if (!props.form.cordinates) {
                warningText.value = 'To ensure the event reaches the widest audience, please choose a location from the list, ideally providing the name of the venue.';
            }
        }, 10000);

    }, 4000);

    const myHeaders = new Headers();
    myHeaders.append('X-API-KEY', key);
    myHeaders.append('Content-Type', 'application/json');

    const raw = JSON.stringify({
        'q': data.queryText,
        'gl': 'am'
    });

    const requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: raw
    };

    fetch('https://google.serper.dev/places', requestOptions)
        .then(response => response.json())
        .then(response => {
            data.places = response.places.filter(item => {
                item.name = item.title + ' ' + item.address;
                return item;
            });
        })
        .catch(error => console.log('error', error));
};

watch(() => data.selectedCountry, (value) => {
    props.form.location = value.name ?? value;

    if (value.latitude) {
        warningText.value = false;
        props.form.cordinates = { 'latitude': value.latitude, 'longitude': value.longitude };
        props.form.cid = value.cid;
    } else {
        props.form.cordinates = null;
    }
});


</script>

<template>
    <div>
        <AutoComplete class="w-full"
                      v-model="data.selectedCountry"
                      v-debounce:1500="getPlacesQuery"
                      optionLabel="name"
                      :suggestions="data.places"
                      @complete="searchCountries"
                      placeholder="Name of the establishment, address" />
        <ErrorMessages v-if="warningText" class="mt-10"
                       :messages="{text: warningText}" />
    </div>
</template>
