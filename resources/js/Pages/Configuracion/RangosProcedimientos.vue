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
const canCreate = computed(() => can.value.includes('rangos_procedimientos.store'))
const canUpdate = computed(() => can.value.includes('rangos_procedimientos.update'))
const canDelete = computed(() => can.value.includes('rangos_procedimientos.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    RangosProcedimientos: Object,
    TiposProcesos: Array, // [{id,nombre}]
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    tipo_proceso_id: null,
    clave: '',
    nombre: '',
    monto_min: '',
    monto_max: '',
    activo: true,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Tipo proceso', key: 'dato_tipo_proceso', sortable: false },
    { title: 'Clave', key: 'clave', sortable: true },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Monto min', key: 'monto_min', sortable: true },
    { title: 'Monto max', key: 'monto_max', sortable: true },
    { title: 'Activo', key: 'activo', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const tipoNombreById = (id) => {
    const t = (props.TiposProcesos ?? []).find((x) => x.id === id)
    return t ? t.nombre : '—'
}

const fmtMoney = (v) => {
    if (v === null || v === undefined || v === '') return '—'
    const n = Number(v)
    if (Number.isNaN(n)) return '—'
    return n.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })
}

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ResetForm = () => {
    form.reset()
    form.id = null
    form.tipo_proceso_id = null
    form.activo = true
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.tipo_proceso_id = item.tipo_proceso_id ?? null
        form.clave = item.clave ?? ''
        form.nombre = item.nombre ?? ''
        form.monto_min = item.monto_min ?? ''
        form.monto_max = item.monto_max ?? ''
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
                title: form.id ? 'Rango actualizado' : 'Rango registrado',
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
        form.put(route('rangos_procedimientos.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('rangos_procedimientos.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('rangos_procedimientos.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Rango eliminado', icon: 'success' })
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
    <AppLayout title="Rangos de procedimientos">
        <template #actions>
            <VButton
                v-if="canCreate"
                prepend-icon="mdi-table-plus"
                @click="ChangeModal()"
            >
                Nuevo rango
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Rangos de Procedimientos"
                searchable
                search-label="Buscar rango"
                search-placeholder="Clave o nombre..."
                :server-route="route('rangos_procedimientos.index')"
                server-prop="RangosProcedimientos"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.dato_tipo_proceso`]="{ item }">
                    {{ tipoNombreById(item.tipo_proceso_id) }}
                </template>

                <template v-slot:[`item.monto_min`]="{ item }">
                    {{ fmtMoney(item.monto_min) }}
                </template>

                <template v-slot:[`item.monto_max`]="{ item }">
                    {{ item.monto_max === null ? 'Sin tope' : fmtMoney(item.monto_max) }}
                </template>

                <template v-slot:[`item.activo`]="{ item }">
                    <span
                        class="px-2 py-1 text-xs rounded"
                        :class="(item.activo ?? true) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                        {{ (item.activo ?? true) ? 'Sí' : 'No' }}
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
            :title="form.id ? 'Editar rango' : 'Nuevo rango'"
            header-icon="mdi-table"
            max-width="750"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <MdSelect
                                v-model="form.tipo_proceso_id"
                                label="Tipo de proceso"
                                icon="mdi-playlist-check"
                                :items="TiposProcesos"
                                item-value="id"
                                item-title="nombre"
                                :required="true"
                                clearable
                            />
                        </div>

                        <MdTextInput
                            v-model="form.clave"
                            label="Clave"
                            icon="mdi-key-outline"
                            :required="true"
                            :maxLength="20"
                            counter
                        />

                        <div class="md:col-span-2">
                            <MdTextInput
                                v-model="form.nombre"
                                label="Nombre"
                                icon="mdi-text"
                                :required="true"
                                :maxLength="150"
                                counter
                            />
                        </div>

                        <MdTextInput
                            v-model="form.monto_min"
                            label="Monto mínimo"
                            icon="mdi-cash-minus"
                            :required="true"
                            :allowed="'decimal'"
                            :maxLength="20"
                        />

                        <MdTextInput
                            v-model="form.monto_max"
                            label="Monto máximo (vacío = sin tope)"
                            icon="mdi-cash-plus"
                            :allowed="'decimal'"
                            :maxLength="20"
                        />

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
