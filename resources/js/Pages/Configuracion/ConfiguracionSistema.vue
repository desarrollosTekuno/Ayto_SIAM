<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import DataTableServer from '@/Components/DataTableServer.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import VButton from '@/Components/Vuetify/VButton.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdSelect from '@/Components/MaterialDesign/MdSelect.vue'

import { customToastSwal, warningToast, errorToast } from '@/utils/swal'

// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('configuraciones_sistema.store'))
const canUpdate = computed(() => can.value.includes('configuraciones_sistema.update'))
const canDelete = computed(() => can.value.includes('configuraciones_sistema.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    Configuraciones: Object,
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    nombre: '',
    descripcion: '',
    valor: '',
    tipo: 'string',
    activo: true,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Clave', key: 'clave', sortable: true },
    { title: 'Valor', key: 'valor', sortable: false },
    { title: 'Tipo', key: 'tipo', sortable: false },
    { title: 'Activo', key: 'activo', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ResetForm = () => {
    form.reset()
    form.id = null
    form.tipo = 'string'
    form.activo = true
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.nombre = item.nombre ?? ''
        form.descripcion = item.descripcion ?? ''
        form.valor = item.valor ?? ''
        form.tipo = item.tipo ?? 'string'
        form.activo = item.activo ?? true
    } else {
        if (!canCreate.value) return
        ResetForm()
    }

    showModal.value = true
}

const GuardarModificar = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({
                title: form.id
                    ? 'Configuración actualizada'
                    : 'Configuración registrada',
                icon: 'success',
            })
            showModal.value = false
            ResetForm()
            ReloadTable()
        },
        onError: () => errorToast('Ocurrió un error'),
    }

    if (form.id) {
        if (!canUpdate.value) return
        form.put(route('configuraciones_sistema.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('configuraciones_sistema.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('configuraciones_sistema.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Configuración eliminada', icon: 'success' })
            ReloadTable()
        },
        onError: () => errorToast('Ocurrió un error'),
    })
}

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados')
}
</script>

<template>
    <AppLayout title="Configuración del sistema">
        <template #actions>
            <VButton
                v-if="canCreate"
                prepend-icon="mdi-cog-plus-outline"
                @click="ChangeModal()"
            >
                Nueva configuración
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Configuraciones del sistema"
                searchable
                search-label="Buscar configuración"
                search-placeholder="Clave o valor..."
                server-route="configuraciones_sistema.index"
                server-prop="Configuraciones"
                :headers="headers"
                :items-per-page="5"
            >
                <template v-slot:[`item.activo`]="{ item }">
                    <span
                        class="px-2 py-1 text-xs rounded"
                        :class="item.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                        {{ item.activo ? 'Sí' : 'No' }}
                    </span>
                </template>

                <template v-slot:[`item.actions`]="{ item }">
                    <div class="flex" v-if="canUpdate || canDelete">
                        <VButton
                            v-if="canUpdate"
                            size="x-small"
                            variant="flat"
                            color="teal-darken-1"
                            icon="mdi-pencil-outline"
                            class="mr-2"
                            @click="ChangeModal(item)"
                        />

                        <VButton
                            v-if="canDelete"
                            size="x-small"
                            variant="flat"
                            color="red-darken-1"
                            icon="mdi-trash-can-outline"
                            @click="Eliminar(item.id)"
                        />
                    </div>
                </template>
            </DataTableServer>
        </section>

        <!-- MODAL -->
        <VDialog
            v-model="showModal"
            :title="form.id ? 'Editar configuración' : 'Nueva configuración'"
            header-icon="mdi-cog-outline"
            max-width="650"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <MdTextInput
                            v-model="form.nombre"
                            label="Clave"
                            icon="mdi-key-outline"
                            :required="true"
                            :maxLength="50"
                            counter
                        />

                        <MdSelect
                            v-model="form.tipo"
                            label="Tipo"
                            icon="mdi-format-list-bulleted"
                            :items="[
                                { id: 'string', nombre: 'Texto' },
                                { id: 'number', nombre: 'Número' },
                                { id: 'boolean', nombre: 'Booleano' },
                                { id: 'json', nombre: 'JSON' },
                            ]"
                            item-value="id"
                            item-title="nombre"
                        />

                        <div class="md:col-span-2">
                            <MdTextInput
                                v-model="form.valor"
                                label="Valor"
                                icon="mdi-database-outline"
                                :maxLength="255"
                                counter
                            />
                        </div>

                        <div class="md:col-span-2">
                            <MdTextInput
                                v-model="form.descripcion"
                                label="Descripción"
                                icon="mdi-text-box-outline"
                                :maxLength="255"
                                counter
                            />
                        </div>

                        <div class="md:col-span-2">
                            <MdSelect
                                v-model="form.activo"
                                label="Activo"
                                icon="mdi-check-circle-outline"
                                :items="[
                                    { id: true, nombre: 'Sí' },
                                    { id: false, nombre: 'No' },
                                ]"
                                item-value="id"
                                item-title="nombre"
                            />
                        </div>
                    </div>
                </FormValidate>
            </template>

            <template #footer="{ close }">
                <VBtnCancel prepend-icon="mdi-close" @click="close">
                    Cancelar
                </VBtnCancel>

                <VBtnSend
                    v-if="(form.id && canUpdate) || (!form.id && canCreate)"
                    prepend-icon="mdi-content-save-outline"
                    @click="formValidateRef?.submit()"
                >
                    {{ form.id ? 'Actualizar' : 'Guardar' }}
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
