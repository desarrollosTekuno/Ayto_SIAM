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
import MdSelectSearch from '@/Components/MaterialDesign/MdSelectSearch.vue'

import { customToastSwal, warningToast, errorToast } from '@/utils/swal'
import { customConfirmSwal } from '@/utils/swal'

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
const formValidateRef = ref(null)
const DTableRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    nombre: '',
    apellido_paterno: '',
    apellido_materno: '',
    correo: '',
    telefono: '',
    extension: '',
    cargo_id: null,
    user_id: null,
})

// =============================== OPTIONS ===============================
const cargosOptions = computed(() =>
    (props.Cargos ?? []).map((c) => ({
        label: c.nombre,
        value: c.id,
    }))
)

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'nombre_completo', sortable: true },
    { title: 'Cargo', key: 'cargo', sortable: false },
    { title: 'Correo', key: 'correo', sortable: true },
    { title: 'Teléfono', key: 'telefono', sortable: true },
    { title: 'Acciones', key: 'actions', sortable: false },
]

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const NombreCompleto = (item) => {
    const n = item?.nombre ?? ''
    const ap = item?.apellido_paterno ?? ''
    const am = item?.apellido_materno ?? ''
    return [n, ap, am].filter(Boolean).join(' ').trim()
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.nombre = item.nombre ?? ''
        form.apellido_paterno = item.apellido_paterno ?? ''
        form.apellido_materno = item.apellido_materno ?? ''
        form.correo = item.correo ?? ''
        form.telefono = item.telefono ?? ''
        form.extension = item.extension ?? ''
        form.cargo_id = item.cargo_id ?? null
        form.user_id = item.user_id ?? null
    } else {
        if (!canCreate.value) return
        form.reset()
        form.clearErrors()
    }

    showModal.value = true
}

const GuardarModificar = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
        customToastSwal({
            title: form.id ? 'Titular actualizado correctamente' : 'Titular registrado correctamente',
            icon: 'success',
        })
            showModal.value = false
            form.reset()
            form.clearErrors()
            ReloadTable()
        },
        onError: () => errorToast('Ocurrió un error'),
    }

    if (form.id) {
        form.put(route('titulares.update', form.id), options)
    } else {
        form.post(route('titulares.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    customConfirmSwal({
        title: "¿Está segur@ que desea eliminar este registro?",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('titulares.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    customToastSwal({
                        title: 'Titular eliminado',
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
    <AppLayout title="Titulares">
        <template #actions>
            <VButton v-if="canCreate" prepend-icon="mdi-plus" @click="ChangeModal()">
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
                <template v-slot:[`item.nombre_completo`]="{ item }">
                    {{ NombreCompleto(item) }}
                </template>

                <template v-slot:[`item.cargo`]="{ item }">
                    <span v-if="item?.cargo?.nombre">{{ item.cargo.nombre }}</span>
                    <span v-else>
                        {{ cargosOptions.find((c) => c.value === item?.cargo_id)?.label ?? '—' }}
                    </span>
                </template>

                <!-- Acciones -->
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
        <VDialog v-model="showModal" :title="form.id ? 'Editar titular' : 'Nuevo titular'" header-icon="mdi-account-tie-outline" max-width="700">
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre(s)"
                            icon="mdi-account-outline"
                            :required="true"
                            :minLength="2"
                            :maxLength="150"
                            counter
                            :external-error="form.errors.nombre"
                        />

                        <MdSelectSearch
                            v-model="form.cargo_id"
                            label="Cargo"
                            :items="Cargos"
                            item-title="nombre"
                            item-value="id"
                            icon="mdi-briefcase-outline"
                            :clearable="true"
                            :required="false"
                            :external-error="form.errors.cargo_id"
                        />

                        <MdTextInput
                            v-model="form.apellido_paterno"
                            label="Apellido paterno"
                            icon="mdi-account-details-outline"
                            :required="false"
                            :minLength="2"
                            :maxLength="150"
                            counter
                            :external-error="form.errors.apellido_paterno"
                        />

                        <MdTextInput
                            v-model="form.apellido_materno"
                            label="Apellido materno"
                            icon="mdi-account-details-outline"
                            :required="false"
                            :minLength="2"
                            :maxLength="150"
                            counter
                            :external-error="form.errors.apellido_materno"
                        />

                        <MdTextInput
                            v-model="form.correo"
                            label="Correo"
                            type="email"
                            icon="mdi-email-outline"
                            :required="false"
                            :maxLength="95"
                            counter
                            :external-error="form.errors.correo"
                        />

                        <MdTextInput
                            v-model="form.telefono"
                            label="Teléfono"
                            icon="mdi-phone-outline"
                            :required="false"
                            :maxLength="20"
                            counter
                            :external-error="form.errors.telefono"
                        />

                        <MdTextInput
                            v-model="form.extension"
                            label="Extensión"
                            icon="mdi-phone-in-talk-outline"
                            :required="false"
                            :maxLength="10"
                            counter
                            :external-error="form.errors.extension"
                        />

                        <!-- Opcional -->
                        <MdTextInput
                            v-model="form.user_id"
                            label="User ID (opcional)"
                            type="number"
                            icon="mdi-account-key-outline"
                            :required="false"
                            :external-error="form.errors.user_id"
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
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="formValidateRef?.submit()"
                >
                    {{ form.id ? 'Actualizar' : 'Guardar' }}
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
