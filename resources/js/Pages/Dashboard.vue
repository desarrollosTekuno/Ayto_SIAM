<script setup lang="ts">
//@ts-nocheck
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

interface Props {
    categories?: any;
}

const props = withDefaults(defineProps<Props>(), {
    categories: null,
});
</script>

<template>
    <Head title="Inicio" />

    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <span>Inicio</span>
                <span />
            </div>
        </template>

        <div class="min-h-screen bg-gray-50">

            <div class="text-white rounded bg-app-primary">
                <div class="max-w-6xl px-6 py-6 mx-auto font-semibold md:font-black font-md text-normal md:text-xl">
                    <h1 class="font-bold tracking-wide text-center uppercase">
                        Sistema integral de adjudicaciones municipales
                    </h1>
                </div>
            </div>

            <div class="max-w-6xl px-4 mx-auto py-14">
                <div class="overflow-hidden bg-white border border-gray-200 shadow-lg rounded-2xl">
                    <div class="h-1 bg-app-primary/70" />

                    <div class="p-10">
                        <div class="flex flex-col items-center gap-6 text-center">
                            <img
                                src="/Img/logoTk.png"
                                alt="Logo Puebla"
                                class="w-auto h-32 md:h-36"
                            />

                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 md:text-2xl">
                                    Puebla
                                </h2>
                                <p class="mt-1 text-gray-600">
                                    Sistema integral de adjudicaciones municipales
                                </p>
                            </div>

                            <div class="w-full h-px max-w-md bg-gray-200" />

                            <p class="text-sm text-gray-500">
                                Dirección de adjudicaciones
                            </p>
                        </div>
                    </div>

                    <!-- FOOTER TARJETA -->
                    <div class="px-6 py-4 border-t bg-gray-50">
                        <p class="text-xs text-center text-gray-500">
                            H. Ayuntamiento de Puebla · Puebla, Pue., México
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
