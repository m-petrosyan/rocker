<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="loader"></div>
    </div>

</template>
<style>
/* HTML: <div class="loader"></div> */
.loader {
    width: 40px;
    aspect-ratio: 1;
    --c: linear-gradient(#FFF 0 0);
    --r1: radial-gradient(farthest-side at bottom, #FFF 93%, #FFF);
    --r2: radial-gradient(farthest-side at top, #FFF 93%, #FFF);
    background: var(--c), var(--r1), var(--r2),
    var(--c), var(--r1), var(--r2),
    var(--c), var(--r1), var(--r2);
    background-repeat: no-repeat;
    animation: l2 1s infinite alternate;
}

@keyframes l2 {
    0%, 25% {
        background-size: 8px 0, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px;
        background-position: 0 50%, 0 calc(50% - 2px), 0 calc(50% + 2px), 50% 50%, 50% calc(50% - 2px), 50% calc(50% + 2px), 100% 50%, 100% calc(50% - 2px), 100% calc(50% + 2px);
    }
    50% {
        background-size: 8px 100%, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px;
        background-position: 0 50%, 0 calc(0% - 2px), 0 calc(100% + 2px), 50% 50%, 50% calc(50% - 2px), 50% calc(50% + 2px), 100% 50%, 100% calc(50% - 2px), 100% calc(50% + 2px);
    }
    75% {
        background-size: 8px 100%, 8px 4px, 8px 4px, 8px 100%, 8px 4px, 8px 4px, 8px 0, 8px 4px, 8px 4px;
        background-position: 0 50%, 0 calc(0% - 2px), 0 calc(100% + 2px), 50% 50%, 50% calc(0% - 2px), 50% calc(100% + 2px), 100% 50%, 100% calc(50% - 2px), 100% calc(50% + 2px);
    }
    95%, 100% {
        background-size: 8px 100%, 8px 4px, 8px 4px, 8px 100%, 8px 4px, 8px 4px, 8px 100%, 8px 4px, 8px 4px;
        background-position: 0 50%, 0 calc(0% - 2px), 0 calc(100% + 2px), 50% 50%, 50% calc(0% - 2px), 50% calc(100% + 2px), 100% 50%, 100% calc(0% - 2px), 100% calc(100% + 2px);
    }
}
</style>
<script setup lang="ts">
</script>
