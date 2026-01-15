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
const canCreate = computed(() => can.value.includes('usuarios.store'))
const canUpdate = computed(() => can.value.includes('usuarios.update'))
const canDelete = computed(() => can.value.includes('usuarios.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    Usuarios: Object,
    UnidadesAdministrativas: Array, // [{id, nombre}]
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',

    // user_datos
    cargo: '',
    telefono: '',
    extension: '',
    activo: true,
    unidad_administrativa_id: null,
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'name', sortable: true },
    { title: 'Usuario', key: 'username', sortable: true },
    { title: 'Correo', key: 'email', sortable: true },
    { title: 'Cargo', key: 'dato_cargo', sortable: false },
    { title: 'Unidad', key: 'dato_unidad', sortable: false },
    { title: 'Activo', key: 'dato_activo', sortable: false },
    { title: 'Acciones', key: 'actions', sortable: false },
]

const unidadNombreById = (id) => {
    const u = (props.UnidadesAdministrativas ?? []).find((x) => x.id === id)
    return u ? u.nombre : 'Sin unidad'
}

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.()
}

const ResetForm = () => {
    form.reset()
    form.id = null
    form.activo = true
    form.unidad_administrativa_id = null
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.name = item.name ?? ''
        form.username = item.username ?? ''
        form.email = item.email ?? ''

        // No precargamos password
        form.password = ''
        form.password_confirmation = ''

        // user_datos (vienen por with('Dato'))
        const d = item.Dato ?? item.dato ?? null
        form.cargo = d?.cargo ?? ''
        form.telefono = d?.telefono ?? ''
        form.extension = d?.extension ?? ''
        form.activo = (d?.activo ?? true) ? true : false
        form.unidad_administrativa_id = d?.unidad_administrativa_id ?? null
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
                title: form.id ? 'Usuario actualizado correctamente' : 'Usuario registrado correctamente',
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
        form.put(route('usuarios.update', form.id), options)
    } else {
        if (!canCreate.value) return
        form.post(route('usuarios.store'), options)
    }
}

const Eliminar = (id) => {
    if (!canDelete.value) return

    form.delete(route('usuarios.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({ title: 'Usuario eliminado', icon: 'success' })
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
    <AppLayout title="Usuarios">
        <template #actions>
            <VButton
                v-if="canCreate"
                prepend-icon="mdi-account-plus-outline"
                @click="ChangeModal()"
            >
                Nuevo Usuario
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Usuarios"
                searchable
                search-label="Buscar usuario"
                search-placeholder="Escribe el nombre o usuario..."
                server-route="usuarios.index"
                server-prop="Usuarios"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.dato_cargo`]="{ item }">
                    {{ (item.Dato ?? item.dato)?.cargo ?? '—' }}
                </template>

                <template v-slot:[`item.dato_unidad`]="{ item }">
                    {{ unidadNombreById((item.Dato ?? item.dato)?.unidad_administrativa_id) }}
                </template>

                <template v-slot:[`item.dato_activo`]="{ item }">
                    <span
                        class="px-2 py-1 text-xs rounded"
                        :class="((item.Dato ?? item.dato)?.activo ?? true) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                        {{ ((item.Dato ?? item.dato)?.activo ?? true) ? 'Sí' : 'No' }}
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
            :title="form.id ? 'Editar usuario' : 'Nuevo usuario'"
            header-icon="mdi-account-cog-outline"
            max-width="700"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <MdTextInput
                                v-model="form.name"
                                label="Nombre"
                                icon="mdi-account-outline"
                                :required="true"
                                :minLength="3"
                                :maxLength="150"
                                counter
                            />
                        </div>

                        <MdTextInput
                            v-model="form.username"
                            label="Usuario"
                            icon="mdi-at"
                            :required="true"
                            :minLength="3"
                            :maxLength="75"
                            counter
                        />

                        <MdTextInput
                            v-model="form.email"
                            label="Correo"
                            icon="mdi-email-outline"
                            type="email"
                            :maxLength="100"
                            counter
                            :allowed="'any'"
                            :pattern="/^$|^[^\s@]+@[^\s@]+\.[^\s@]+$/"
                        />

                        <MdTextInput
                            v-model="form.password"
                            :label="form.id ? 'Contraseña (dejar vacío para no cambiar)' : 'Contraseña'"
                            icon="mdi-lock-outline"
                            type="password"
                            :required="!form.id"
                            :minLength="form.id ? 0 : 8"
                            :maxLength="100"
                        />

                        <MdTextInput
                            v-model="form.password_confirmation"
                            :label="form.id ? 'Confirmar contraseña (si cambiaste)' : 'Confirmar contraseña'"
                            icon="mdi-lock-check-outline"
                            type="password"
                            :required="!form.id"
                            :minLength="form.id ? 0 : 8"
                            :maxLength="100"
                        />

                        <div class="md:col-span-2">
                            <MdSelect
                                v-model="form.unidad_administrativa_id"
                                label="Unidad administrativa"
                                icon="mdi-office-building-outline"
                                :items="UnidadesAdministrativas"
                                item-value="id"
                                item-title="nombre"
                                clearable
                            />
                        </div>

                        <MdTextInput
                            v-model="form.cargo"
                            label="Cargo (texto)"
                            icon="mdi-briefcase-outline"
                            :maxLength="150"
                            counter
                        />

                        <MdTextInput
                            v-model="form.telefono"
                            label="Teléfono"
                            icon="mdi-phone-outline"
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
