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
    Municipios: Array,
})

const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)
const Municipios = ref([])

// =============================== FORM ===============================
const form = useForm({
    id: null,

    nombre: '',
    cveDep: '',
    cveURes: '',
    abreviatura: '',
    usado_en: 'SIAM',

    nombre_titular: '',
    cargo_titular: '',
    telefono: '',
    extension: '',

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
    { title: 'Abrev.', key: 'abreviatura', sortable: false },
    { title: 'Titular', key: 'nombre_titular', sortable: false },
    { title: 'Teléfono', key: 'telefono', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ResetForm = () => {
    form.reset()
    form.id = null
    form.usado_en = 'SIAM'
    form.estado_id = null
    form.municipio_id = null
}

const ChangeModal = (item = null) => {
    console.log(item);

    if (item) {
        if (!canUpdate.value) return
            form.id = item.id
            form.nombre = item.nombre ?? ''
            form.cveDep = item.cveDep ?? ''
            form.cveURes = item.cveURes ?? ''
            form.abreviatura = item.abreviatura ?? ''
            form.usado_en = item.usado_en ?? 'SIAM'

            form.nombre_titular = item.datos.nombre_titular ?? ''
            form.cargo_titular = item.datos.cargo_titular ?? ''
            form.telefono = item.datostelefono ?? ''
            form.extension = item.datos.extension ?? ''

            form.calle = item.direccion.calle ?? ''
            form.numero_exterior = item.direccion.numero_exterior ?? ''
            form.numero_interior = item.direccion.numero_interior ?? ''
            form.colonia = item.direccion.colonia ?? ''
            form.codigo_postal = item.direccion.codigo_postal ?? ''
            form.estado_id = item.direccion.estado_id ?? null
            form.municipio_id = item.direccion.municipio_id ?? null
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

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados')
}

// =============================== WATCHERS ===============================
watch(() => form.estado_id,
    async (estadoId) => {
        form.municipio_id = null
        Municipios.value = []

        try {
        const { data } = await axios.get(route('estados.municipios', estadoId))
            Municipios.value = data
        } catch (e) {
            warning.log('No se pudieron obtener los municipios', e);
        }
    }
)
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
        <VDialog v-model="showModal" :title="form.id ? 'Editar dependencia' : 'Nueva dependencia'" header-icon="mdi-office-building-outline" max-width="900">
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- DEPENDENCIA -->
                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            icon="mdi-domain"
                            :required="true"
                            :minLength="3"
                            :maxLength="150"
                            counter
                        />

                        <MdTextInput
                            v-model="form.abreviatura"
                            label="Abreviatura"
                            icon="mdi-text-short"
                            :maxLength="100"
                            counter
                        />

                        <MdTextInput
                            v-model="form.cveDep"
                            label="Clave Dependencia (cveDep)"
                            icon="mdi-identifier"
                            :maxLength="5"
                            counter
                        />

                        <MdTextInput
                            v-model="form.cveURes"
                            label="Clave URes (cveURes)"
                            icon="mdi-identifier"
                            :maxLength="4"
                            counter
                        />

                        <!-- DATOS TITULAR -->
                        <div class="pt-2 border-t md:col-span-2 border-slate-200">
                            <div class="px-2 text-sm font-semibold text-slate-700">
                                Datos del titular
                            </div>
                        </div>

                        <MdTextInput
                            v-model="form.nombre_titular"
                            label="Nombre del titular"
                            icon="mdi-account-tie-outline"
                            :required="true"
                            :maxLength="150"
                            counter
                        />

                        <MdTextInput
                            v-model="form.cargo_titular"
                            label="Cargo del titular"
                            icon="mdi-badge-account-outline"
                            :required="true"
                            :maxLength="150"
                            counter
                        />

                        <MdTextInput
                            v-model="form.telefono"
                            label="Teléfono"
                            icon="mdi-phone"
                            :required="true"
                            :maxLength="20"
                            counter
                        />

                        <MdTextInput
                            v-model="form.extension"
                            label="Extensión"
                            icon="mdi-phone-in-talk-outline"
                            :maxLength="10"
                            counter
                        />

                        <!-- DIRECCIÓN -->
                        <div class="pt-2 border-t md:col-span-2 border-slate-200">
                            <div class="px-2 text-sm font-semibold text-slate-700">
                                Dirección
                            </div>
                        </div>

                        <MdTextInput
                            v-model="form.calle"
                            label="Calle"
                            icon="mdi-road-variant"
                            :required="true"
                            :maxLength="150"
                            counter
                        />

                        <MdTextInput
                            v-model="form.numero_exterior"
                            label="Número exterior"
                            icon="mdi-home-outline"
                            :required="true"
                            :maxLength="20"
                            counter
                        />

                        <MdTextInput
                            v-model="form.numero_interior"
                            label="Número interior"
                            icon="mdi-door"
                            :maxLength="20"
                            counter
                        />

                        <MdTextInput
                            v-model="form.colonia"
                            label="Colonia"
                            icon="mdi-map-marker-outline"
                            :required="true"
                            :maxLength="120"
                            counter
                        />

                        <MdTextInput
                            v-model="form.codigo_postal"
                            label="Código postal"
                            icon="mdi-mailbox-outline"
                            :required="true"
                            :maxLength="10"
                            counter
                        />

                        <MdSelect
                            v-model="form.estado_id"
                            label="Estado"
                            :items="Estados"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />

                        <MdSelect
                            v-model="form.municipio_id"
                            label="Municipio"
                            :items="Municipios"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />
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
