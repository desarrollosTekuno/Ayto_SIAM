<!-- AppLayout.vue -->
<script setup lang="ts">
//@ts-nocheck
import Loader from '@/Components/Loader.vue';
import Navigation from '@/Layouts/Navigation.vue';
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

            <v-app-bar prominent>
                <v-app-bar-nav-icon
                    :icon="$vuetify.display.mobile ? (!drawer ? 'mdi-backburger' : 'mdi-menu') : 'mdi-menu'"
                    variant="text"
                    @click.stop="clicStop($vuetify.display.mobile)"
                />
                <v-spacer />
            </v-app-bar>

            <Navigation :rail="rail" v-model:drawer="drawer" />

            <v-main>
                <div class="p-6">
                    <h3 class="mb-4 text-3xl font-medium text-gray-700 font-poppins">
                        <slot name="header">
                            {{ title }}
                        </slot>
                    </h3>

                    <slot />
                </div>
            </v-main>

            <Loader :overlay="isLoading" />
        </v-app>
    </v-responsive>
</template>
