<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTableServer from '@/Components/DataTableServer.vue'

const props = defineProps({
    items: Object,
})

// Headers (columnas)
const headers = [
    { title: 'ID',                  key: 'id',                    sortable: true },
    { title: 'Hechizo',             key: 'hechizo',               sortable: true },
    { title: 'Ingrediente',         key: 'ingrediente_principal', sortable: true },
    { title: 'Nivel',               key: 'nivel_hechizo',         sortable: true },
    { title: 'Canal',               key: 'canal_hechizo',         sortable: true },
    { title: 'Fecha Ritual',        key: 'fecha_ritual',          sortable: true },
    { title: 'Rango Mago',          key: 'rango_mago',            sortable: true },
    { title: 'Acciones',            key: 'actions',               sortable: false },
]
</script>

<template>
    <Head title="Ejemplos de Hechizos" />

    <AppLayout>
        <template #header>
            <div class="w-full text-center">
                <span class="my-2 text-xl font-extrabold text-emerald-600">
                    Ejemplos de Hechizos
                </span>
            </div>
        </template>

        <section class="p-4">
            <DataTableServer
                server-route="Example.index"
                server-prop="items"
                :headers="headers"
                density="compact"
                item-value="id"
                :items-per-page="5"
                :items-per-page-options="[5, 15, 20, 25]"
            >
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn
                    size="x-small"
                    variant="text"
                    icon="mdi-eye"
                    @click="console.log('Ver hechizo', item.id)"
                />
            </template>
            </DataTableServer>
        </section>
    </AppLayout>
</template>
