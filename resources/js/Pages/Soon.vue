<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Number of divs to display
const totalDivs = 70;
// Total number of images available
const totalImages = 21;
// Interval in milliseconds for image changes
const changeInterval = 2000;

const divs = ref(Array(totalDivs).fill().map(() => ({
    currentImageIndex: Math.floor(Math.random() * totalImages),
    imageSrc: '',
    flickerClass: Math.random() < 0.5 ? 'flicker-i' : 'flicker-t'
})));

// Function to update the image source for all divs
// Function to update the image source for all divs
const updateImages = () => {
    divs.value.forEach((div, index) => {
        // Randomly determine whether to change this image
        if (Math.random() < 0.7) { // 70% chance to change
            div.currentImageIndex = Math.floor(Math.random() * totalImages);
            div.imageSrc = `./images/${div.currentImageIndex}.jpg`;
        }

        // Occasionally change the flicker class
        if (Math.random() < 0.2) { // 20% chance to change flicker class
            // Use index to determine which class to apply
            div.flickerClass = index % 2 === 0 ? 'flicker-i' : 'flicker-t';
        }
    });
};

// Initialize image sources
const initializeImages = () => {
    divs.value.forEach((div) => {
        div.imageSrc = `./images/${div.currentImageIndex}.jpg`;
    });
};

let intervalId = null;

onMounted(() => {
    // Set initial images
    initializeImages();

    // Start the interval for changing images
    intervalId = setInterval(updateImages, changeInterval);
});

onBeforeUnmount(() => {
    // Clean up interval when component is destroyed
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>


<template>
    <GuestLayout>
        <section>
            <div
                v-for="(div, index) in divs"
                :key="index"
                :class="div.flickerClass"
            >
                <img :src="div.imageSrc" :alt="`Image ${div.currentImageIndex}`">
            </div>
            <div class="bg"></div>
            <div class="logo">
                <img src="/images/logo.png" />
            </div>
        </section>
    </GuestLayout>
</template>


<style scoped>
:root {
    --white: #f1f1f1
}

:global(body) {
    margin: 0;
    font-family: Montserrat, sans-serif;
    font-size: 40px;
    letter-spacing: 0.7em;
    background-color: #000;
    justify-content: center;
    align-items: center;
    height: 100vh;
    overflow: hidden;
}

section {
    display: flex;
    flex-wrap: wrap;
}

section div {
    width: 10%;
}

section div img {
    width: 100%;
}

.logo {
    width: 300px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.bg {
    position: absolute;
    top: 0;
    left: 0;
    height: 100vh;
    width: 100vw;
    background-color: rgba(0, 0, 0, 0.58);
}

.logo img {

}

div > span {
    opacity: 0.15;
}

.flicker-i {
    opacity: 1;
    animation: flickerI 2s linear reverse infinite;
}

.flicker-i::after {
    content: '';
    width: 150%;
    -webkit-box-shadow: -35px 0px 60px 8px rgba(255, 255, 255, 1);
    -moz-box-shadow: -35px 0px 60px 8px rgba(255, 255, 255, 1);
    box-shadow: -35px 0px 60px 8px rgba(255, 255, 255, 1);
}

.flicker-l, .flicker-g {
    animation: flickerLG 2s linear reverse infinite;
    position: relative;
}

.flicker-l::after,
.flicker-l::before {
    content: ' ';
    width: 100px;
    height: 100px;
    background: var(--white);
    position: absolute;
    top: -50%;
    left: 100%;
    border-radius: 100%;
    opacity: 0.05;
    filter: blur(10px);
}

.flicker-l::after {
    width: 200px;
    height: 200px;
    top: -150%;
    left: -5%;
    opacity: 0.1;
    filter: blur(10px);
}

.flicker-h {
    animation: flickerH 2s linear reverse infinite
}

.flicker-t {
    animation: flickerH 2s linear reverse infinite
}


@keyframes flickerI {
    0% {
        opacity: 0.4;
    }
    5% {
        opacity: 0.5;
    }
    10% {
        opacity: 0.6;
    }
    15% {
        opacity: 0.85;
    }
    25% {
        opacity: 0.5;
    }
    30% {
        opacity: 1;

    }
    35% {
        opacity: 0.1;
    }
    40% {
        opacity: 0.25;
    }
    45% {
        opacity: 0.5;
    }
    60% {
        opacity: 1;

    }
    70% {
        opacity: 0.85;
    }
    80% {
        opacity: 0.4;
    }
    90% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;

    }
}

@keyframes flickerLG {
    0% {
        opacity: 0.19;
    }
    5% {
        opacity: 0.2;
    }
    10% {
        opacity: 0.25;
    }
    15% {
        opacity: 0.35;
    }
    25% {
        opacity: 0.2;
    }
    30% {
        opacity: 0.4;
    }
    35% {
        opacity: 0.1;
    }
    40% {
        opacity: 0.25;
    }
    45% {
        opacity: 0.2;
    }
    60% {
        opacity: 0.4;
    }
    70% {
        opacity: 0.35;
    }
    80% {
        opacity: 0.4;
    }
    90% {
        opacity: 0.2;
    }
    100% {
        opacity: 0.4;
    }
}

@keyframes flickerH {
    0% {
        opacity: 0.15;
    }
    5% {
        opacity: 0.2;
    }
    10% {
        opacity: 0.12;
    }
    15% {
        opacity: 0.2;
    }
    25% {
        opacity: 0.15;
    }
    30% {
        opacity: 0.4;
    }
    35% {
        opacity: 0.05;
    }
    40% {
        opacity: 0.12;
    }
    45% {
        opacity: 0.15;
    }
    60% {
        opacity: 0.3;
    }
    70% {
        opacity: 0.2;
    }
    80% {
        opacity: 0.3;
    }
    90% {
        opacity: 0.18;
    }
    100% {
        opacity: 0.3;
    }
}

@keyframes flickerT {
    0% {
        opacity: 0.01;
    }
    5% {
        opacity: 0.05;
    }
    10% {
        opacity: 0.03;
    }
    15% {
        opacity: 0.1;
    }
    25% {
        opacity: 0.07;
    }
    30% {
        opacity: 0.1;
    }
    35% {
        opacity: 0.05;
    }
    40% {
        opacity: 0.06;
    }
    45% {
        opacity: 0.1;
    }
    60% {
        opacity: 0.05;
    }
    70% {
        opacity: 0.1;
    }
    80% {
        opacity: 0.05;
    }
    90% {
        opacity: 0.0;
    }
    100% {
        opacity: 0.1;
    }
}
</style>
