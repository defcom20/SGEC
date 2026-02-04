<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <ToastContainer />
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
