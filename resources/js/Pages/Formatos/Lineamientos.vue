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
import MdFileInput from '@/Components/MaterialDesign/MdFileInput.vue'

import { customToastSwal, warningToast, errorToast } from '@/utils/swal'
import { customConfirmSwal } from '@/utils/swal'

// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('lineamientos.store'))
const canUpdate = computed(() => can.value.includes('lineamientos.update'))
const canDelete = computed(() => can.value.includes('lineamientos.destroy'))

// =============================== PROPS ===============================
defineProps({
    Formatos: Object,
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    nombre: '',
    titulo: '',
    archivo: null,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id' },
    { title: 'Nombre', key: 'nombre' },
    { title: 'Título', key: 'titulo' },
    { title: 'Archivo', key: 'archivo' },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const ReloadTable = () => DTableRef.value?.reload?.()

const ResetForm = () => {
    form.reset()
    form.id = null
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return
        form.id = item.id
        form.nombre = item.nombre
        form.titulo = item.titulo
        form.archivo = null
    } else {
        if (!canCreate.value) return
        ResetForm()
    }
    showModal.value = true
}

// =============================== CRUD ===============================
const GuardarModificar = () => {
    const options = {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({
                title: form.id
                    ? 'Archivo actualizado correctamente'
                    : 'Archivo cargado correctamente',
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
        form.post(route('lineamientos.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('lineamientos.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    customConfirmSwal({
        title: "¿Está segur@ que desea eliminar este registro?",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('lineamientos.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    customToastSwal({
                        title: 'Archivo eliminado',
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

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados')
}
</script>

<template>
    <AppLayout title="Lineamientos – Formatos">
        <template #actions>
            <VButton
                v-if="canCreate"
                prepend-icon="mdi-upload"
                @click="ChangeModal()"
            >
                Cargar archivo
            </VButton>
        </template>

        <!-- TABLA -->
        <DataTableServer
        ref="DTableRef"
        title="Formatos de lineamientos"
        server-route="lineamientos.index"
        server-prop="Formatos"
        searchable
        search-placeholder="Buscar archivo…"
        :headers="headers"
        >
        <template #[`item.archivo`]="{ item }">
            <a v-if="item.archivo" :href="`/storage/${item.archivo}`" target="_blank" class="px-2 py-1 cursor-pointer text-md text-customSurface rounded-xl bg-customMuted">
                <span class="mdi mdi-tray-arrow-down"></span> Ver archivo
            </a>
        </template>

        <template #[`item.actions`]="{ item }">
            <div class="flex gap-2" v-if="canUpdate || canDelete">
                <VButton
                    v-if="canUpdate"
                    icon="mdi-pencil-outline"
                    size="x-small"
                    variant="flat"
                    color="teal-darken-1"
                    @click="ChangeModal(item)"
                />
                <VButton
                    v-if="canDelete"
                    icon="mdi-trash-can-outline"
                    size="x-small"
                    variant="flat"
                    color="red-darken-1"
                    @click="Eliminar(item.id)"
                />
            </div>
        </template>
        </DataTableServer>

        <!-- MODAL -->
        <VDialog
            v-model="showModal"
            :title="form.id ? 'Editar archivo' : 'Cargar archivo'"
            header-icon="mdi-file-document-outline"
            max-width="700"
        >
            <template #content>
                <FormValidate
                    ref="formValidateRef"
                    @submit="GuardarModificar"
                    @invalid="onInvalidForm"
                >
                    <div class="grid grid-cols-1">
                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            :required="true"
                            :maxLength="100"
                        />

                        <MdTextInput
                            v-model="form.titulo"
                            label="Título"
                            :maxLength="850"
                        />

                        <MdFileInput
                            v-model="form.archivo"
                            label="Archivo"
                            :required="!form.id"
                            accept=".pdf,.doc,.docx,.xls,.xlsx"
                            :maxSizeMB="10"
                            helper="PDF, Word o Excel. Máx 10 MB"
                        />
                    </div>
                </FormValidate>
            </template>

            <template #footer="{ close }">
                <VBtnCancel prepend-icon="mdi-close" @click="close">
                    Cancelar
                </VBtnCancel>

                <VBtnSend
                    prepend-icon="mdi-content-save-outline"
                    @click="formValidateRef?.submit()"
                >
                    {{ form.id ? 'Actualizar' : 'Guardar' }}
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
