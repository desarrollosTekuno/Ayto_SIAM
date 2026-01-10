<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import VButton from '@/Components/Vuetify/VButton.vue';
import DataTableServer from '@/Components/DataTableServer.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import { useForm } from '@inertiajs/vue3'
import { customToastSwal, warningToast, errorToast } from '@/utils/swal'

// =============================== PROPS ===============================
const props = defineProps({
    Areas: Object,
})

// =============================== STATE ===============================
const showModal = ref(false)
const editMode = ref(false)
const formValidateRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    nombre: '',
    abreviatura: '',
    alias: '',
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Abreviatura', key: 'abreviatura', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

// =============================== METHODS ===============================
const ChangeModal = (item = null) => {
    if (item) {
        editMode.value = true
        form.id = item.id
        form.nombre = item.nombre
        form.abreviatura = item.abreviatura
        form.alias = item.alias
    } else {
        editMode.value = false
        form.reset()
    }

    showModal.value = true
}

const GuardarModificar = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({
                title: editMode.value
                    ? 'Área actualizada correctamente'
                    : 'Área registrada correctamente',
                icon: 'success',
            })
            showModal.value = false
            form.reset()
        },
        onError: () => {
            errorToast('Ocurrió un error')
        },
    }

    if (form.id) {
        form.put(route('areas.update', form.id), options)
    } else {
        form.post(route('areas.store'), options)
    }
}

const Eliminar = (id) => {
    form.delete(route('areas.destroy', id), {
        preserveScroll: true,
        onSuccess: () =>
            customToastSwal({
                title: 'Área eliminada',
                icon: 'success',
            }),
    })
}

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados')
}

onMounted(() => {})
</script>

<template>
    <AppLayout title="Áreas">
        <template #actions>
            <VButton prepend-icon="mdi-plus" @click="ChangeModal()">
                Nuevo Registro
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                title="Catálogo de Áreas"
                searchable
                search-label="Buscar área"
                search-placeholder="Escribe el nombre..."
                server-route="areas.index"
                server-prop="Areas"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.actions`]="{ item }">
                    <div class="flex">
                        <VButton
                            size="x-small"
                            variant="flat"
                            color="teal-darken-1"
                            icon="mdi-pencil-outline"
                            @click="ChangeModal(item.raw)"
                            class="mr-2"
                        />

                        <VButton
                            size="x-small"
                            variant="flat"
                            color="red-darken-1"
                            icon="mdi-trash-can-outline"
                            @click="Eliminar(item.raw.id)"
                        />
                    </div>
                </template>
            </DataTableServer>
        </section>

        <!-- MODAL -->
        <VDialog
            v-model="showModal"
            :title="editMode ? 'Editar área' : 'Nueva área'"
            header-icon="mdi-domain-outline"
            max-width="600"
        >
            <template #content>
                <FormValidate
                    ref="formValidateRef"
                    @submit="GuardarModificar"
                    @invalid="onInvalidForm"
                >
                    <div class="grid grid-cols-1 gap-4">
                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            :required="true"
                            :minLength="3"
                            :maxLength="100"
                            counter
                        />

                        <MdTextInput
                            v-model="form.abreviatura"
                            label="Abreviatura"
                            :maxLength="100"
                            counter
                        />

                        <MdTextInput
                            v-model="form.alias"
                            label="Alias"
                            :maxLength="100"
                            counter
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
                    Guardar
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
