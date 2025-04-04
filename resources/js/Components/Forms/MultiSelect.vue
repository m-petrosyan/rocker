<script setup>
import Multiselect from 'vue-multiselect';
import { ref } from 'vue';

const value = ref([]);
const options = [
    { name: 'Ildaruni', code: '54' },
    { name: 'Bambir', code: '12' },
    { name: 'Dogma', code: '04' }
];

const removeItem = (item) => {
    value.value = value.value.filter(v => v.name !== item.name);
};
</script>

<template>
    <div>
        <label class="typo__label">Bands</label>
        <multiselect
            id="option-tags"
            v-model="value"
            :options="options"
            :multiple="true"
            placeholder="Type to search"
            track-by="name"
            label="name"
        >
            <!-- Слот для кастомизации тегов -->
            <template v-slot:tag="{ option }">
                <span class="multiselect__tag">
                    <span>{{ option.name }}</span>
                    <button
                        @click="removeItem(option)"
                        class="multiselect__tag-icon"
                        type="button"
                        aria-label="Remove"
                    >
                        ×
                    </button>
                </span>
            </template>
            <template v-slot:noResult>
                Oops! No elements found. Consider changing the search query.
            </template>
        </multiselect>
        <pre class="language-json"><code>{{ value }}</code></pre>
    </div>
</template>

<style scoped lang="scss">
:deep(.multiselect) {
    color: black;

    & .multiselect__tags {
        padding: 6px 4px;
        background-color: white;
        border-radius: 5px;

        & .multiselect__tag {
            color: white;
            background-color: theme('colors.green');
            display: inline-flex;
            align-items: center;
            margin: 2px;
            padding: 2px 7px;
            border-radius: 4px;

            .multiselect__tag-icon {
                background: none;
                border: none;
                margin-left: 5px;
                cursor: pointer;
                font-size: 16px;
                color: #666;
                padding: 0;
                line-height: 1;

                &:hover {
                    color: theme('colors.orange');
                }
            }
        }
    }

    & .multiselect__content-wrapper {
        background-color: white;

        .multiselect__content {
            width: 100%;

            & .multiselect__element {
                padding: 10px 15px;
                cursor: pointer;

                .multiselect__option {
                    display: block;
                }

                &[aria-selected="true"]:hover {
                    background-color: theme('colors.orange');
                    color: white;
                }

                &:hover:not([aria-selected="true"]) {
                    background-color: theme('colors.green');
                    color: white;
                }
            }
        }
    }
}
</style>
