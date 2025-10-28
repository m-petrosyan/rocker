<script setup lang="ts">

</script>

<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black/30 z-50">
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
    border: 1px solid theme('colors.orange');
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
      left: 0;
      top: -12px;
      width: 36px;
      height: 36px;
    }

    &:nth-child(3) {
      border-width: 2px;
      left: -3px;
      top: 9px;
      width: 54px;
      height: 54px;
    }

    &:nth-child(4) {
      border-width: 3px;
      left: -3px;
      top: -12px;
      width: 69px;
      height: 69px;
    }

    &:nth-child(5) {
      border-width: 3px;
      left: -3px;
      top: 12px;
      width: 93px;
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
