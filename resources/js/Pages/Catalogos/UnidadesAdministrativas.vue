<!-- resources/js/Pages/Catalogos/UnidadesAdministrativas.vue -->
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
import { customConfirmSwal } from '@/utils/swal'

// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('unidades_administrativas.store'))
const canUpdate = computed(() => can.value.includes('unidades_administrativas.update'))
const canDelete = computed(() => can.value.includes('unidades_administrativas.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    UnidadesAdministrativas: Object,
    UnidadesPadre: Array,
    Dependencias: Array,
    Titulares: Array,
    Estados: Array,
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
    nombre: '',
    siglas: '',
    abreviatura: '',
    alias: '',
    tipo: 0,
    dependencia_id: null,
    titular_id: null,
    unidad_padre_id: null,

    // datos
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
    { title: 'Tipo', key: 'tipo_text', sortable: false },
    { title: 'Dependencia', key: 'dependencia', sortable: false },
    { title: 'Titular', key: 'titular', sortable: false },
    { title: 'Teléfono', key: 'dato.telefono', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

// =============================== HELPERS ===============================
const tipoItems = [
    { id: 0, nombre: 'Dirección' },
    { id: 1, nombre: 'Departamento' },
    { id: 2, nombre: 'Jefatura' },
]

const TipoText = (tipo) => tipoItems.find((x) => x.id === Number(tipo))?.nombre ?? '—'

// =============================== METHODS ===============================
const ReloadTable = () => DTableRef.value?.reload?.()

const ResetForm = () => {
    form.reset()
    form.clearErrors()

    form.id = null
    form.tipo = 0
    form.dependencia_id = null
    form.titular_id = null
    form.unidad_padre_id = null

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
        form.siglas = item.siglas ?? ''
        form.abreviatura = item.abreviatura ?? ''
        form.alias = item.alias ?? ''
        form.tipo = item.tipo ?? 0

        form.dependencia_id = item.dependencia_id ?? null
        form.titular_id = item.titular_id ?? null
        form.unidad_padre_id = item.unidad_padre_id ?? null

        form.telefono = item.dato?.telefono ?? ''
        form.extension = item.dato?.extension ?? ''

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
            ? 'Unidad administrativa actualizada correctamente'
            : 'Unidad administrativa registrada correctamente',
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
        form.put(route('unidades_administrativas.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('unidades_administrativas.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    customConfirmSwal({
        title: "¿Está segur@ que desea eliminar este registro?",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('unidades_administrativas.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    customToastSwal({
                        title: 'Unidad administrativa eliminada',
                        icon: 'success'
                    })
                    ReloadTable()
                },
                onError: (err) => {
                    console.error(err);
                    customToastSwal({
                        title: "Error al eliminar el registro",
                        icon: "error",
                    });
                },
            })
        }
    });


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
        } else {
        const exists = Municipios.value.some((m) => m.id === form.municipio_id)
        if (!exists) form.municipio_id = null
        }
    } catch (e) {
        Municipios.value = []
        form.municipio_id = null
        console.info('No se pudieron obtener los municipios', e)
    } finally {
        loadingMunicipios.value = false
    }
}

const onInvalidForm = () => warningToast('Revisa los campos marcados')

// =============================== WATCHERS ===============================
watch(() => form.estado_id,
    async (newValue, oldValue) => {
        if (newValue && newValue !== oldValue) {
            await ConsultaMunicipios(newValue, { keepSelected: false })
        }
        if (!newValue) {
            Municipios.value = []
            form.municipio_id = null
        }
    }
)
</script>

<template>
    <AppLayout title="Unidades Administrativas">
        <template #actions>
            <VButton v-if="canCreate" prepend-icon="mdi-plus" @click="ChangeModal()">
                Nuevo Registro
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Unidades Administrativas"
                searchable
                search-label="Buscar unidad"
                search-placeholder="Escribe el nombre..."
                server-route="unidades_administrativas.index"
                server-prop="UnidadesAdministrativas"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.tipo_text`]="{ item }">
                {{ TipoText(item?.tipo) }}
                </template>

                <template v-slot:[`item.dependencia`]="{ item }">
                <span class="text-slate-400" v-if="!item?.dependencia">—</span>
                <span v-else>{{ item.dependencia?.cveDep ? `${item.dependencia.cveDep} - ` : '' }}{{ item.dependencia.nombre }}</span>
                </template>

                <template v-slot:[`item.titular`]="{ item }">
                <span class="text-slate-400" v-if="!item?.titular">—</span>
                <span v-else>
                    {{ item.titular?.nombre }} {{ item.titular?.apellido_paterno }} {{ item.titular?.apellido_materno }}
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
        <VDialog v-model="showModal" :title="form.id ? 'Editar unidad administrativa' : 'Nueva unidad administrativa'" header-icon="mdi-sitemap-outline" max-width="900">
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="font-semibold md:col-span-2 text-slate-700">
                            Datos generales
                        </div>

                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            icon="mdi-office-building-outline"
                            :required="true"
                            :minLength="3"
                            :maxLength="150"
                            counter
                            :external-error="form.errors.nombre"
                        />

                        <MdSelect
                            v-model="form.tipo"
                            label="Tipo"
                            :items="tipoItems"
                            item-title="nombre"
                            item-value="id"
                            clearable
                        />

                        <MdTextInput
                            v-model="form.siglas"
                            label="Siglas"
                            icon="mdi-alphabetical-variant"
                            :maxLength="50"
                            counter
                            :external-error="form.errors.siglas"
                        />

                        <MdTextInput
                            v-model="form.abreviatura"
                            label="Abreviatura"
                            icon="mdi-text-short"
                            :maxLength="100"
                            counter
                            :external-error="form.errors.abreviatura"
                        />

                        <MdTextInput
                            v-model="form.alias"
                            label="Alias"
                            icon="mdi-tag-outline"
                            :maxLength="100"
                            counter
                            :external-error="form.errors.alias"
                        />

                        <MdSelect
                            v-model="form.dependencia_id"
                            label="Dependencia"
                            :items="Dependencias"
                            item-title="nombre"
                            item-value="id"
                            clearable
                        />

                        <MdSelect
                            v-model="form.unidad_padre_id"
                            label="Unidad padre"
                            :items="UnidadesPadre"
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
                            Datos de contacto de la unidad
                        </div>

                        <MdTextInput
                            v-model="form.telefono"
                            label="Teléfono"
                            icon="mdi-phone"
                            :maxLength="20"
                            counter
                            :external-error="form.errors.telefono"
                        />

                        <MdTextInput
                            v-model="form.extension"
                            label="Extensión"
                            icon="mdi-phone-in-talk-outline"
                            :maxLength="10"
                            counter
                            :external-error="form.errors.extension"
                        />

                        <!-- DIRECCIÓN -->
                        <div class="pt-2 font-semibold border-t md:col-span-2 text-slate-700">
                            Dirección
                        </div>

                        <MdTextInput
                            v-model="form.calle"
                            label="Calle"
                            icon="mdi-road-variant"
                            :maxLength="150"
                            counter
                            :external-error="form.errors.calle"
                        />

                        <MdTextInput
                            v-model="form.numero_exterior"
                            label="Número exterior"
                            icon="mdi-home-outline"
                            :maxLength="20"
                            counter
                            :external-error="form.errors.numero_exterior"
                        />

                        <MdTextInput
                            v-model="form.numero_interior"
                            label="Número interior"
                            icon="mdi-door"
                            :maxLength="20"
                            counter
                            :external-error="form.errors.numero_interior"
                        />

                        <MdTextInput
                            v-model="form.colonia"
                            label="Colonia"
                            icon="mdi-map-marker-outline"
                            :maxLength="120"
                            counter
                            :external-error="form.errors.colonia"
                        />

                        <MdTextInput
                            v-model="form.codigo_postal"
                            label="Código postal"
                            icon="mdi-mailbox-outline"
                            :maxLength="10"
                            counter
                            :external-error="form.errors.codigo_postal"
                        />

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
                <VBtnCancel prepend-icon="mdi-close" @click="close">
                    Cancelar
                </VBtnCancel>

                <VBtnSend
                    v-if="(form.id && canUpdate) || (!form.id && canCreate)"
                    prepend-icon="mdi-content-save-outline"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="formValidateRef?.submit()"
                >
                    {{ form.id ? 'Actualizar' : 'Guardar' }}
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
