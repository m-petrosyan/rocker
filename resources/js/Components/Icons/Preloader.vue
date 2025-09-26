<script setup lang="ts">

</script>

<template>
    <div

        class="fixed inset-0 flex items-center justify-center bg-black/30 z-50"
    >
        <div class="loader relative w-[297px] h-[297px]">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.loader {
    animation: rotateAnim 0.4s linear infinite;

    div {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid #ff5722; // можно заменить на var(--tw-text-opacity) если хочешь tailwind цвет
        position: absolute;
        top: 6px;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;

        &:nth-child(odd) {
            border-top: none;
            border-left: none;
        }

        &:nth-child(even) {
            border-bottom: none;
            border-right: none;
        }

        &:nth-child(2) {
            border-width: 2px;
            left: 0px;
            top: -12px; // -4 * 3
            width: 36px; // 12 * 3
            height: 36px;
        }

        &:nth-child(3) {
            border-width: 2px;
            left: -3px; // -1 * 3
            top: 9px; // 3 * 3
            width: 54px; // 18 * 3
            height: 54px;
        }

        &:nth-child(4) {
            border-width: 3px;
            left: -3px;
            top: -12px;
            width: 69px; // 23 * 3
            height: 69px;
        }

        &:nth-child(5) {
            border-width: 3px;
            left: -3px;
            top: 12px; // 4 * 3
            width: 93px; // 31 * 3
            height: 93px;
        }

        &:nth-child(6) {
            border-width: 4px;
            left: 0px;
            top: -4px;
            width: 39px;
            height: 39px;
        }

        &:nth-child(7) {
            border-width: 4px;
            left: 0px;
            top: 6px;
            width: 49px;
            height: 49px;
        }
    }
}

@keyframes rotateAnim {
    from {
        transform: rotate(360deg);
    }
    to {
        transform: rotate(0deg);
    }
}
</style>
