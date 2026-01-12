<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import VButton from '@/Components/Vuetify/VButton.vue'
import DataTableServer from '@/Components/DataTableServer.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdSelect from '@/Components/MaterialDesign/MdSelect.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import { customToastSwal, warningToast, errorToast } from '@/utils/swal'

// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('titulares.store'))
const canUpdate = computed(() => can.value.includes('titulares.update'))
const canDelete = computed(() => can.value.includes('titulares.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    Titular: Object,
    Cargos: Array,
})

// =============================== STATE ===============================
const showModal = ref(false)
const editMode = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    nombre: '',
    cargo_id: null,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Cargo', key: 'cargo_nombre', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const cargoNombreById = (id) => {
    const c = (props.Cargos ?? []).find((x) => x.id === id)
    return c ? c.nombre : 'Sin cargo'
}

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return
        editMode.value = true
        form.id = item.id
        form.nombre = item.nombre
        form.cargo_id = item.cargo_id ?? null
    } else {
        if (!canCreate.value) return
        editMode.value = false
        form.reset()
        form.cargo_id = null
    }

    showModal.value = true
}

const GuardarModificar = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({
                title: form.id
                    ? 'Titular actualizado correctamente'
                    : 'Titular registrado correctamente',
                icon: 'success',
            })
            showModal.value = false
            form.reset()
            form.cargo_id = null
            ReloadTable()
        },
        onError: () => errorToast('Ocurrió un error'),
    }

    if (form.id) {
        if (!canUpdate.value) return
        form.put(route('titulares.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('titulares.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('titulares.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Titular eliminado', icon: 'success' })
            ReloadTable()
        },
    })
}

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados')
}
</script>

<template>
    <AppLayout title="Titulares">
        <template #actions>
            <VButton
                v-if="canCreate"
                prepend-icon="mdi-plus"
                @click="ChangeModal()"
            >
                Nuevo Registro
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Titulares"
                searchable
                search-label="Buscar titular"
                search-placeholder="Escribe el nombre..."
                server-route="titulares.index"
                server-prop="Titular"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.cargo_nombre`]="{ item }">
                    {{ cargoNombreById(item.cargo_id) }}
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
            :title="form.id ? 'Editar titular' : 'Nuevo titular'"
            header-icon="mdi-account-tie-outline"
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
                            :maxLength="150"
                            counter
                        />

                        <MdSelect
                            v-model="form.cargo_id"
                            label="Cargo"
                            :items="Cargos"
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
