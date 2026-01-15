<!-- resources/js/Pages/Catalogos/Dependencias.vue -->
<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

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
const canCreate = computed(() => can.value.includes('dependencias.store'))
const canUpdate = computed(() => can.value.includes('dependencias.update'))
const canDelete = computed(() => can.value.includes('dependencias.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    Dependencias: Object,
    Estados: Array,
    Titulares: Array,
    DependenciasPadre: Array,
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

const Municipios = ref([])
const loadingMunicipios = ref(false)

// =============================== FORM ===============================
const form = useForm({
    id: null,

    // dependencia
    nombre: '',
    cveDep: '',
    cveURes: '',
    abreviatura: '',
    tipo: 0,
    centralizada: 1,
    usado_en: 'SIAM',
    dependencia_padre_id: null,
    titular_id: null,

    // dependencia_datos
    telefono: '',
    extension: '',

    // direccion
    calle: '',
    numero_exterior: '',
    numero_interior: '',
    colonia: '',
    codigo_postal: '',
    estado_id: null,
    municipio_id: null,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Clave', key: 'cveDep', sortable: true },
    { title: 'Abrev.', key: 'abreviatura', sortable: true },
    { title: 'Titular', key: 'titular', sortable: false },
    { title: 'Teléfono', key: 'dato.telefono', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ResetForm = () => {
    form.reset()
    form.clearErrors()

    form.id = null
    form.tipo = 0
    form.centralizada = 1
    form.usado_en = 'SIAM'
    form.dependencia_padre_id = null

    form.estado_id = null
    form.municipio_id = null
    Municipios.value = []
}

const ChangeModal = async (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.clearErrors()

        form.id = item.id
        form.nombre = item.nombre ?? ''
        form.cveDep = item.cveDep ?? ''
        form.cveURes = item.cveURes ?? ''
        form.abreviatura = item.abreviatura ?? ''
        form.dependencia_padre_id = item.dependencia_padre_id ?? null
        form.titular_id = item.titular_id ?? null

        // dependencia_datos
        form.telefono = item.dato?.telefono ?? ''
        form.extension = item.dato?.extension ?? ''

        // direccion
        form.calle = item.direccion?.calle ?? ''
        form.numero_exterior = item.direccion?.numero_exterior ?? ''
        form.numero_interior = item.direccion?.numero_interior ?? ''
        form.colonia = item.direccion?.colonia ?? ''
        form.codigo_postal = item.direccion?.codigo_postal ?? ''
        form.estado_id = item.direccion?.estado_id ?? null
        form.municipio_id = item.direccion?.municipio_id ?? null

        if (form.estado_id) {
            await ConsultaMunicipios(form.estado_id, { keepSelected: true })
        }
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
                    ? 'Dependencia actualizada correctamente'
                    : 'Dependencia registrada correctamente',
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
        form.put(route('dependencias.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('dependencias.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('dependencias.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Dependencia eliminada', icon: 'success' })
            ReloadTable()
        },
        onError: () => errorToast('No se pudo eliminar'),
    })
}

const ConsultaMunicipios = async (estadoId, opts = { keepSelected: false }) => {
    const id = estadoId ?? form.estado_id
    if (!id) {
        Municipios.value = []
        form.municipio_id = null
        return
    }

    loadingMunicipios.value = true
    try {
        const { data } = await axios.get(route('estados.municipios', id))
        Municipios.value = Array.isArray(data) ? data : []

        if (!opts.keepSelected) {
            form.municipio_id = null
        }
    } catch {
        Municipios.value = []
        form.municipio_id = null
    } finally {
        loadingMunicipios.value = false
    }
}

// =============================== WATCHERS ===============================
watch(() => form.estado_id, async (newValue) => {
    if (newValue) {
        await ConsultaMunicipios(newValue)
    } else {
        Municipios.value = []
        form.municipio_id = null
    }
})

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados')
}
</script>

<template>
    <AppLayout title="Dependencias">
        <template #actions>
            <VButton v-if="canCreate" prepend-icon="mdi-plus" @click="ChangeModal()">
                Nuevo Registro
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Dependencias"
                searchable
                search-label="Buscar dependencia"
                search-placeholder="Escribe el nombre..."
                server-route="dependencias.index"
                server-prop="Dependencias"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.titular`]="{ item }">
                    <span v-if="item?.titular">
                        {{ item.titular.nombre }}
                        {{ item.titular.apellido_paterno }}
                        {{ item.titular.apellido_materno }}
                    </span>
                    <span v-else class="text-slate-400">—</span>
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
            :title="form.id ? 'Editar dependencia' : 'Nueva dependencia'"
            header-icon="mdi-office-building-outline"
            max-width="900"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- DATOS GENERALES -->
                        <div class="font-semibold md:col-span-2 text-slate-700">
                            Datos generales
                        </div>

                        <MdTextInput v-model="form.nombre" label="Nombre" icon="mdi-domain" required />
                        <MdTextInput v-model="form.abreviatura" label="Abreviatura" icon="mdi-text-short" />
                        <MdTextInput v-model="form.cveDep" label="Clave Dependencia" icon="mdi-identifier" />
                        <MdTextInput v-model="form.cveURes" label="Clave URes" icon="mdi-identifier" />

                        <MdSelect
                            v-model="form.dependencia_padre_id"
                            label="Dependencia padre"
                            :items="DependenciasPadre"
                            item-title="nombre"
                            item-value="id"
                            clearable
                        />

                        <MdSelect
                            v-model="form.titular_id"
                            label="Titular"
                            :items="Titulares"
                            item-title="nombre"
                            item-value="id"
                            clearable
                        />

                        <!-- CONTACTO -->
                        <div class="pt-2 font-semibold border-t md:col-span-2 text-slate-700">
                            Datos de contacto de la dependencia
                        </div>

                        <MdTextInput v-model="form.telefono" label="Teléfono" icon="mdi-phone" />
                        <MdTextInput v-model="form.extension" label="Extensión" icon="mdi-phone-in-talk-outline" />

                        <!-- DIRECCIÓN -->
                        <div class="pt-2 font-semibold border-t md:col-span-2 text-slate-700">
                            Dirección
                        </div>

                        <MdTextInput v-model="form.calle" label="Calle" icon="mdi-road-variant" required />
                        <MdTextInput v-model="form.numero_exterior" label="Número exterior" icon="mdi-home-outline" required />
                        <MdTextInput v-model="form.numero_interior" label="Número interior" icon="mdi-door" />
                        <MdTextInput v-model="form.colonia" label="Colonia" icon="mdi-map-marker-outline" required />
                        <MdTextInput v-model="form.codigo_postal" label="Código postal" icon="mdi-mailbox-outline" required />

                        <MdSelect
                            v-model="form.estado_id"
                            label="Estado"
                            :items="Estados"
                            item-title="nombre"
                            item-value="id"
                            clearable
                        />

                        <MdSelect
                            v-model="form.municipio_id"
                            label="Municipio"
                            :items="Municipios"
                            item-title="nombre"
                            item-value="id"
                            clearable
                            :disabled="!form.estado_id || loadingMunicipios"
                        />
                    </div>
                </FormValidate>
            </template>

            <template #footer="{ close }">
                <VBtnCancel prepend-icon="mdi-close" @click="close">Cancelar</VBtnCancel>
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
