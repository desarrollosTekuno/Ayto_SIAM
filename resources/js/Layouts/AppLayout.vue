<!-- AppLayout.vue -->
<script setup lang="ts">
//@ts-nocheck
import Loader from '@/Components/Loader.vue';
import Navigation from '@/Layouts/Navigation.vue';
import TopBar from './TopBar.vue';
import { isLoading } from '@/loading';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const { title } = defineProps<{
    title?: string;
}>();

const drawer = ref(true);
const rail = ref(false);

const fullTitle = computed(() =>
    title ? `${title} | SIAM` : 'SIAM'
);

const clicStop = (displayMobile: boolean) => {
    rail.value = !rail.value;

    if (displayMobile) {
        drawer.value = !drawer.value;
    } else {
        drawer.value = true;
    }
};
</script>

<template>
    <v-responsive class="border rounded">
        <v-app>
            <Head :title="fullTitle" />

            <TopBar :title="title ?? 'SIAM'" @toggle-drawer="clicStop($vuetify.display.mobile)"></TopBar>

            <Navigation :rail="rail" v-model:drawer="drawer" />

            <v-main>
                <div class="px-2">

                    <div class="flex justify-end py-3">
                        <slot name="actions"></slot>
                    </div>

                    <slot />
                </div>
            </v-main>

            <Loader :overlay="isLoading" />
        </v-app>
    </v-responsive>
</template>
