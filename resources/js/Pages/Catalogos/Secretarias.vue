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
const canCreate = computed(() => can.value.includes('secretarias.store'))
const canUpdate = computed(() => can.value.includes('secretarias.update'))
const canDelete = computed(() => can.value.includes('secretarias.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    Secretarias: Object,
    Estados: Array,
    Titulares: Array,
    SecretariasPadre: Array,
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)
const Municipios = ref([])

// =============================== FORM ===============================
const form = useForm({
    id: null,

    // secretarias
    nombre: '',
    cveDep: '',
    cveURes: '',
    abreviatura: '',
    tipo: 0,
    usado_en: 'SIAM',
    secretaria_padre_id: null,

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
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Clave', key: 'cveDep', sortable: true },
    { title: 'Abrev.', key: 'abreviatura', sortable: false },
    { title: 'Padre', key: 'secretaria_padre_id', sortable: false },
    { title: 'Titular', key: 'titular', sortable: false },
    { title: 'Teléfono', key: 'dato.telefono', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const ReloadTable = () => DTableRef.value?.reload?.()

const ResetForm = () => {
    form.reset()
    form.id = null
    form.usado_en = 'SIAM'
    form.tipo = 0
    form.estado_id = null
    form.municipio_id = null
    form.secretaria_padre_id = null
    Municipios.value = []
}

const SecretariasPadreFiltradas = computed(() => {
    const id = form.id
    const arr = props.SecretariasPadre ?? []
    if (!id) return arr
    return arr.filter((s) => s.id !== id)
})

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.nombre = item.nombre ?? ''
        form.cveDep = item.cveDep ?? ''
        form.cveURes = item.cveURes ?? ''
        form.abreviatura = item.abreviatura ?? ''
        form.tipo = item.tipo ?? 0
        form.usado_en = item.usado_en ?? 'SIAM'
        form.secretaria_padre_id = item.secretaria_padre_id ?? null

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
                title: form.id ? 'Secretaría actualizada correctamente' : 'Secretaría registrada correctamente',
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
        form.put(route('secretarias.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('secretarias.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('secretarias.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Secretaría eliminada', icon: 'success' })
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
    <AppLayout title="Secretarías">
        <template #actions>
            <VButton v-if="canCreate" prepend-icon="mdi-plus" @click="ChangeModal()">
                Nuevo Registro
            </VButton>
        </template>


        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Secretarías"
                searchable
                search-label="Buscar secretaría"
                search-placeholder="Escribe el nombre..."
                server-route="secretarias.index"
                server-prop="Secretarias"
                :headers="headers"
                :items-per-page="10"
            >

                <template v-slot:[`item.secretaria_padre_id`]="{ item }">
                    <span v-if="!item.secretaria_padre_id" class="text-slate-400">—</span>
                    <span v-else>
                        {{
                        (SecretariasPadre.find(s => s.id === item.secretaria_padre_id)?.nombre)
                        ?? `#${item.secretaria_padre_id}`
                        }}
                    </span>
                </template>

                <template v-slot:[`item.titular`]="{ item }">
                    <span class="text-slate-400" v-if="!(item?.Dato?.titular || item?.dato?.titular)">—</span>
                    <span v-else>
                        {{
                        (() => {
                            const t = item?.Dato?.titular ?? item?.dato?.titular
                            return [t?.nombre, t?.apellido_paterno, t?.apellido_materno]
                            .filter(Boolean)
                            .join(' ')
                        })()
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

        <!-- =============================== MODAL  =============================== -->
        <VDialog
            v-model="showModal"
            :title="form.id ? 'Editar secretaría' : 'Nueva secretaría'"
            header-icon="mdi-domain"
            max-width="700"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <MdTextInput v-model="form.nombre" label="Nombre" icon="mdi-domain" :required="true" :minLength="3" :maxLength="150" counter />
                        <MdTextInput v-model="form.abreviatura" label="Abreviatura" icon="mdi-text-short" :maxLength="100" counter />
                        <MdTextInput v-model="form.cveDep" label="Clave (cveDep)" icon="mdi-identifier" :maxLength="5" counter />
                        <MdTextInput v-model="form.cveURes" label="Clave URes (cveURes)" icon="mdi-identifier" :maxLength="4" counter />

                        <MdSelect
                            v-model="form.tipo"
                            label="Tipo"
                            :items="[
                                { id: 0, nombre: 'SECRETARÍA' },
                                { id: 1, nombre: 'DIRECCIÓN' },
                            ]"
                            item-value="id"
                            item-title="nombre"
                            clearable
                        />

                        <MdSelect
                            v-model="form.secretaria_padre_id"
                            label="Secretaría padre (opcional)"
                            :items="SecretariasPadreFiltradas"
                            item-value="id"
                            item-title="nombre"
                            clearable
                            helper="Útil para subsecretarías/direcciones"
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
