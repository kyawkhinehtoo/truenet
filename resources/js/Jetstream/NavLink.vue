<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    isCollapsed: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'text-blue-600 hover:text-blue-500   bg-blueGray-100 shadow-blueGray-200 shadow-inner  dark:bg-gray-700 dark:text-white '
        : 'text-gray-700 hover:bg-gray-100     bg-white        shadow-gray-900                   dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200';
});
</script>

<template>
    <Link :href="href" class="py-2 text-xs" :class="classes" :style="props.active? { color: $page.props.accent_color }:''" v-if="isCollapsed">
        <slot />
    </Link>
    <Link :href="href" class="text-xs uppercase py-3 px-4 font-bold block rounded-md mt-1"  :class="classes" :style="props.active? { color: $page.props.accent_color }:''" v-else>
        <slot />
    </Link>
</template>
