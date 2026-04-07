<script setup>
import NavLink from '@/Components/NavLink.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <NavLink
        :href="href"
        class="group flex min-h-64 items-center gap-2 rounded-xl border-2 border-dashed border-zinc-800 p-4 transition-all duration-300 hover:border-orange hover:bg-zinc-900"
    >
        <div class="mx-auto flex w-32 flex-col items-center gap-y-4">
            <h2
                class="text-4xl text-zinc-600 transition-transform duration-300 group-hover:scale-125 group-hover:text-orange"
            >
                +
            </h2>
            <h3
                class="text-xs font-bold uppercase tracking-widest text-zinc-600 transition-colors group-hover:text-orange"
            >
                {{ title }}
            </h3>
        </div>
    </NavLink>
</template>
