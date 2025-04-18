<script setup>
import { computed } from 'vue';

const props = defineProps({
    url: {
        type: String,
        default: window.location.href
    },
    title: {
        type: String,
        default: document.title
    }
});

const twitterShareUrl = computed(() => {
    return `https://twitter.com/intent/tweet?url=${encodeURIComponent(props.url)}&text=${encodeURIComponent(props.title)}`;
});

const facebookShareUrl = computed(() => {
    return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(props.url)}`;
});
</script>

<template>
    <div class="flex">
        <a
            :href="facebookShareUrl"
            target="_blank"
            class="p-2"
            aria-label="Share on Facebook"
        >
            <img src="/images/social/facebook.png" alt="Facebook" class="w-6 h-6 text-gray-800" />
        </a>
        <a
            :href="twitterShareUrl"
            target="_blank"
            class="p-2"
            aria-label="Share on Twitter"
        >
            <img src="/images/social/twitter.png" alt="Twitter" class="w-6 h-6 text-gray-800" />
        </a>
    </div>
</template>
