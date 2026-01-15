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
const canCreate = computed(() => can.value.includes('archivos_permitidos_proceso.store'))
const canUpdate = computed(() => can.value.includes('archivos_permitidos_proceso.update'))
const canDelete = computed(() => can.value.includes('archivos_permitidos_proceso.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    ArchivosPermitidos: Object,
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
    nombre: '',
    extensiones: '', // "pdf,jpg,png"
    tamano_max_mb: '',
    obligatorio: false,
    activo: true,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Tipo proceso', key: 'dato_tipo_proceso', sortable: false },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Extensiones', key: 'dato_extensiones', sortable: false },
    { title: 'Max MB', key: 'tamano_max_mb', sortable: true },
    { title: 'Obligatorio', key: 'obligatorio', sortable: false },
    { title: 'Activo', key: 'activo', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const tipoNombreById = (id) => {
    const t = (props.TiposProcesos ?? []).find((x) => x.id === id)
    return t ? t.nombre : '—'
}

const parseExt = (v) => {
    if (!v) return []
    return String(v)
        .split(',')
        .map((x) => x.trim())
        .filter(Boolean)
}

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ResetForm = () => {
    form.reset()
    form.id = null
    form.tipo_proceso_id = null
    form.obligatorio = false
    form.activo = true
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.tipo_proceso_id = item.tipo_proceso_id ?? null
        form.nombre = item.nombre ?? ''
        form.extensiones = item.extensiones ?? ''
        form.tamano_max_mb = item.tamano_max_mb ?? ''
        form.obligatorio = item.obligatorio ?? false
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
                title: form.id ? 'Archivo permitido actualizado' : 'Archivo permitido registrado',
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
        form.put(route('archivos_permitidos_proceso.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('archivos_permitidos_proceso.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('archivos_permitidos_proceso.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Archivo permitido eliminado', icon: 'success' })
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
    <AppLayout title="Archivos permitidos por proceso">
        <template #actions>
            <VButton
                v-if="canCreate"
                prepend-icon="mdi-file-plus-outline"
                @click="ChangeModal()"
            >
                Nuevo archivo permitido
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Archivos Permitidos"
                searchable
                search-label="Buscar"
                search-placeholder="Nombre o extensión..."
                :server-route="route('archivos_permitidos_proceso.index')"
                server-prop="ArchivosPermitidos"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.dato_tipo_proceso`]="{ item }">
                    {{ tipoNombreById(item.tipo_proceso_id) }}
                </template>

                <template v-slot:[`item.dato_extensiones`]="{ item }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="(e, idx) in parseExt(item.extensiones)"
                            :key="idx"
                            class="px-2 py-0.5 text-xs rounded bg-slate-100 text-slate-800"
                        >
                            .{{ e }}
                        </span>
                        <span v-if="parseExt(item.extensiones).length === 0">—</span>
                    </div>
                </template>

                <template v-slot:[`item.obligatorio`]="{ item }">
                    <span
                        class="px-2 py-1 text-xs rounded"
                        :class="(item.obligatorio ?? false) ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800'"
                    >
                        {{ (item.obligatorio ?? false) ? 'Sí' : 'No' }}
                    </span>
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
            :title="form.id ? 'Editar archivo permitido' : 'Nuevo archivo permitido'"
            header-icon="mdi-file-cog-outline"
            max-width="800"
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

                        <div class="md:col-span-2">
                            <MdTextInput
                                v-model="form.extensiones"
                                label="Extensiones (separadas por coma)"
                                icon="mdi-file-outline"
                                :required="true"
                                :maxLength="150"
                                counter
                                placeholder="pdf,jpg,png"
                            />
                            <p class="mt-1 text-xs text-slate-500">
                                Ejemplo: pdf,jpg,png (sin puntos). El sistema lo normaliza.
                            </p>
                        </div>

                        <MdTextInput
                            v-model="form.tamano_max_mb"
                            label="Tamaño máximo (MB)"
                            icon="mdi-database-outline"
                            :allowed="'decimal'"
                            :maxLength="10"
                            placeholder="10"
                        />

                        <MdSelect
                            v-model="form.obligatorio"
                            label="Obligatorio"
                            icon="mdi-alert-circle-outline"
                            :items="[
                                { id: true, nombre: 'Sí' },
                                { id: false, nombre: 'No' },
                            ]"
                            item-value="id"
                            item-title="nombre"
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
