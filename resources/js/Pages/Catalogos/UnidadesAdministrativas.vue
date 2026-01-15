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
const canCreate = computed(() => can.value.includes('unidades_administrativas.store'))
const canUpdate = computed(() => can.value.includes('unidades_administrativas.update'))
const canDelete = computed(() => can.value.includes('unidades_administrativas.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    UnidadesAdministrativas: Object,
    UnidadesPadre: Array,
    Dependencias: Array,
    Estados: Array,
    Titulares: Array,
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)
const Municipios = ref([])

// =============================== FORM ===============================
const form = useForm({
    id: null,

    nombre: '',
    siglas: '',
    abreviatura: '',
    alias: '',
    tipo: 0, // 0 DIRECCION, 1 DEPARTAMENTO, 2 JEFATURA
    unidad_padre_id: null,
    dependencia_id: null,

    titular_id: null,
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
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Siglas', key: 'siglas', sortable: true },
    { title: 'Abrev.', key: 'abreviatura', sortable: false },
    { title: 'Tipo', key: 'tipo', sortable: true },
    { title: 'Dependencia', key: 'dependencia_id', sortable: false },
    { title: 'Padre', key: 'unidad_padre_id', sortable: false },
    { title: 'Titular', key: 'titular', sortable: false },
    { title: 'Teléfono', key: 'dato.telefono', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const ReloadTable = () => DTableRef.value?.reload?.()

const ResetForm = () => {
    form.reset()
    form.id = null
    form.tipo = 0
    form.unidad_padre_id = null
    form.dependencia_id = null
    form.estado_id = null
    form.municipio_id = null
    Municipios.value = []
}

const UnidadesPadreFiltradas = computed(() => {
    const id = form.id
    const arr = props.UnidadesPadre ?? []
    if (!id) return arr
    return arr.filter((u) => u.id !== id)
})

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.nombre = item.nombre ?? ''
        form.siglas = item.siglas ?? ''
        form.abreviatura = item.abreviatura ?? ''
        form.alias = item.alias ?? ''
        form.tipo = item.tipo ?? 0
        form.unidad_padre_id = item.unidad_padre_id ?? null
        form.dependencia_id = item.dependencia_id ?? null

        const dato = item.Dato ?? item.dato ?? null
        form.titular_id = dato?.titular_id ?? null
        form.telefono = dato?.telefono ?? ''
        form.extension = dato?.extension ?? ''

        const dir = item.Direccion ?? item.direccion ?? null
        form.calle = dir?.calle ?? ''
        form.numero_exterior = dir?.numero_exterior ?? ''
        form.numero_interior = dir?.numero_interior ?? ''
        form.colonia = dir?.colonia ?? ''
        form.codigo_postal = dir?.codigo_postal ?? ''
        form.estado_id = dir?.estado_id ?? null
        form.municipio_id = dir?.municipio_id ?? null

        if (form.estado_id) ConsultaMunicipios(form.estado_id)
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

    form.delete(route('unidades_administrativas.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Unidad administrativa eliminada', icon: 'success' })
            ReloadTable()
        },
        onError: () => errorToast('No se pudo eliminar'),
    })
}

const ConsultaMunicipios = async (estadoId) => {
    if (!estadoId) {
        form.municipio_id = null
        Municipios.value = []
        return
    }

    form.municipio_id = null
    Municipios.value = []

    try {
        const { data } = await axios.get(route('estados.municipios', estadoId))
        Municipios.value = data
    } catch (e) {
        console.info('No se pudieron obtener los municipios', e)
    }
}

const onInvalidForm = () => warningToast('Revisa los campos marcados')

watch(
    () => form.estado_id,
    (val) => ConsultaMunicipios(val)
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
                search-label="Buscar unidad administrativa"
                search-placeholder="Escribe el nombre..."
                server-route="unidades_administrativas.index"
                server-prop="UnidadesAdministrativas"
                :headers="headers"
                :items-per-page="10"
            >
                <!-- Dependencia (nombre) -->
                <template v-slot:[`item.dependencia_id`]="{ item }">
                    <span v-if="!item.dependencia_id" class="text-slate-400">—</span>
                    <span v-else>
                        {{
                            (Dependencias.find(d => d.id === item.dependencia_id)?.nombre)
                            ?? `#${item.dependencia_id}`
                        }}
                    </span>
                </template>

                <!-- Padre (nombre) -->
                <template v-slot:[`item.unidad_padre_id`]="{ item }">
                    <span v-if="!item.unidad_padre_id" class="text-slate-400">—</span>
                    <span v-else>
                        {{
                            (UnidadesPadre.find(u => u.id === item.unidad_padre_id)?.nombre)
                            ?? `#${item.unidad_padre_id}`
                        }}
                    </span>
                </template>

                <!-- Titular (nombre completo) -->
                <template v-slot:[`item.titular`]="{ item }">
                    <span class="text-slate-400" v-if="!(item?.Dato?.Titular || item?.dato?.titular || item?.Dato?.titular)">—</span>
                    <span v-else>
                        {{
                            (() => {
                                const t = item?.Dato?.Titular ?? item?.Dato?.titular ?? item?.dato?.titular
                                return [t?.nombre, t?.apellido_paterno, t?.apellido_materno]
                                    .filter(Boolean)
                                    .join(' ')
                            })()
                        }}
                    </span>
                </template>

                <!-- Tipo -->
                <template v-slot:[`item.tipo`]="{ item }">
                    <span class="text-slate-700">
                        {{
                            item.tipo === 0 ? 'DIRECCIÓN'
                            : item.tipo === 1 ? 'DEPARTAMENTO'
                            : item.tipo === 2 ? 'JEFATURA'
                            : item.tipo
                        }}
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
            :title="form.id ? 'Editar unidad administrativa' : 'Nueva unidad administrativa'"
            header-icon="mdi-office-building-cog-outline"
            max-width="700"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- UNIDAD ADMINISTRATIVA -->
                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            icon="mdi-office-building-cog-outline"
                            :required="true"
                            :minLength="3"
                            :maxLength="150"
                            counter
                        />

                        <MdTextInput
                            v-model="form.siglas"
                            label="Siglas"
                            icon="mdi-alphabetical-variant"
                            :maxLength="50"
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
                            v-model="form.alias"
                            label="Alias"
                            icon="mdi-tag-outline"
                            :maxLength="100"
                            counter
                        />

                        <MdSelect
                            v-model="form.tipo"
                            label="Tipo"
                            :items="[
                                { id: 0, nombre: 'DIRECCIÓN' },
                                { id: 1, nombre: 'DEPARTAMENTO' },
                                { id: 2, nombre: 'JEFATURA' },
                            ]"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />

                        <MdSelect
                            v-model="form.dependencia_id"
                            label="Dependencia"
                            :items="Dependencias"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />

                        <MdSelect
                            v-model="form.unidad_padre_id"
                            label="Unidad padre (opcional)"
                            :items="UnidadesPadreFiltradas"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />

                        <div class="pt-2 border-t md:col-span-2 border-slate-200">
                            <div class="px-2 text-sm font-semibold text-slate-700">Datos del titular</div>
                        </div>

                        <MdSelect
                            v-model="form.titular_id"
                            label="Persona titular"
                            :items="Titulares"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />

                        <MdTextInput v-model="form.telefono" label="Teléfono" icon="mdi-phone" :required="true" :maxLength="20" counter />
                        <MdTextInput v-model="form.extension" label="Extensión" icon="mdi-phone-in-talk-outline" :maxLength="10" counter />

                        <div class="pt-2 border-t md:col-span-2 border-slate-200">
                            <div class="px-2 text-sm font-semibold text-slate-700">Dirección</div>
                        </div>

                        <MdTextInput v-model="form.calle" label="Calle" icon="mdi-road-variant" :required="true" :maxLength="150" counter />
                        <MdTextInput v-model="form.numero_exterior" label="Número exterior" icon="mdi-home-outline" :required="true" :maxLength="20" counter />
                        <MdTextInput v-model="form.numero_interior" label="Número interior" icon="mdi-door" :maxLength="20" counter />
                        <MdTextInput v-model="form.colonia" label="Colonia" icon="mdi-map-marker-outline" :required="true" :maxLength="120" counter />
                        <MdTextInput v-model="form.codigo_postal" label="Código postal" icon="mdi-mailbox-outline" :required="true" :maxLength="10" counter />

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
                            :disabled="!form.estado_id"
                            helper="Primero selecciona un estado"
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
