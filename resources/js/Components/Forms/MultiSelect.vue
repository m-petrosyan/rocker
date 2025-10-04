<script setup>
import Multiselect from 'vue-multiselect';
import { ref, watch } from 'vue';

const props = defineProps({
    text: { type: String, default: 'Select' },
    multiple: { type: Boolean, default: false },
    modelValue: { type: [Array, Object, String, null], default: () => [] },
    options: { type: Array, default: () => [] },
    disabled: { type: Boolean, default: false }
});

const emits = defineEmits(['update:modelValue']);

const selected = ref(props.multiple ? [...props.modelValue] : props.modelValue ? [{ name: props.modelValue }] : []);

const removeItem = (item) => {
    if (props.multiple) {
        selected.value = selected.value.filter((v) => v.name !== item.name);
    } else {
        selected.value = [...props.modelValue];
    }
};

const addTag = (name) => {
    const tag = { name };
    if (props.multiple) {
        selected.value = [...selected.value, tag];
    } else {
        selected.value = tag;
    }
};

watch(selected, (val) => emits('update:modelValue', val), { deep: true });
</script>

<template>
    <div>
        <multiselect
            :disabled="disabled"
            id="option-tags"
            v-model="selected"
            :options="options"
            :taggable="true"
            @tag="addTag"
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
        </multiselect>
    </div>
</template>

<style scoped lang="scss">
:deep(.multiselect) {
    position: relative;
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
        position: absolute;
        width: 100%;
        overflow-y: auto;
        background-color: theme('colors.graydark2');
        color: white;
        z-index: 111;

        .multiselect__content {
            width: 100%;

            & .multiselect__element {
                padding: 10px 15px;
                cursor: pointer;

                .multiselect__option {
                    display: block;
                }

                &[aria-selected='true']:hover {
                    background-color: theme('colors.orange');
                    color: white;
                }

                &:hover:not([aria-selected='true']) {
                    background-color: theme('colors.green');
                    color: white;
                }
            }
        }
    }
}
</style>
