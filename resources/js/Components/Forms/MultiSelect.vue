<script setup>
import Multiselect from 'vue-multiselect';
import { ref } from 'vue';

defineProps({
    text: {
        type: String,
        default: 'Select'
    },
    multiple: {
        type: Boolean,
        default: false
    }
});

const value = ref([]);
const options = [
    { name: 'Ildaruni', code: '54' },
    { name: 'Bambir', code: '12' },
    { name: 'Dogma', code: '04' }
];

const removeItem = (item) => {
    if (Array.isArray(value.value)) {
        value.value = value.value.filter((v) => v.name !== item.name);
    } else {
        value.value = null;
    }
};
</script>

<template>
    <div>
        <multiselect
            id="option-tags"
            v-model="value"
            :options="options"
            :multiple="multiple"
            :placeholder="text"
            track-by="name"
            label="name"
        >
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
        <!-- <pre class="language-json"><code>{{ value }}</code></pre> -->
    </div>
</template>

<style scoped lang="scss">
:deep(.multiselect) {
    color: black;

    & .multiselect__tags {
        padding: 10px 10px;
        background-color: theme('colors.graydark2');
        color: theme('colors.gray');
        border-radius: 5px;

        .multiselect__tags-wrap .multiselect__input {
            background-color: theme('colors.green');
        }

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
                padding: 0;
                line-height: 1;

                &:hover {
                    color: theme('colors.orange');
                }
            }
        }
    }

    & .multiselect__content-wrapper {
        background-color: theme('colors.graydark2');
        color: white;

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
