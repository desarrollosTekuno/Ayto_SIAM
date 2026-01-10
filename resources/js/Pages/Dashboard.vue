<script setup lang="ts">
//@ts-nocheck
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

interface Props {
    categories?: any;
}

interface Category {
    id: number | null;
    name: string;
    description: string;
}

const props = withDefaults(defineProps<Props>(), {
    categories: null,
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #header> Inicio </template>

        <div class="flex items-center justify-center m-auto h-[75vh]">
            <img src="/Img/logoTk.png" alt="LogoPuebla" class="h-72">
        </div>
    </AppLayout>
</template>
