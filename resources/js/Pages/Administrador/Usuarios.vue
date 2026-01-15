<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

import AppLayout from '@/Layouts/AppLayout.vue'
import VButton from '@/Components/Vuetify/VButton.vue'
import DataTableServer from '@/Components/DataTableServer.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdSelect from '@/Components/MaterialDesign/MdSelect.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import { customConfirmSwal, customToastSwal, warningToast, errorToast } from '@/utils/swal'
import MdCheckbox from '@/Components/MaterialDesign/MdCheckbox.vue'

// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('usuarios.store'))
const canUpdate = computed(() => can.value.includes('usuarios.update'))
const canDelete = computed(() => can.value.includes('usuarios.destroy'))

// =============================== PROPS ===============================
const props = defineProps({
    Usuarios: Object,
    UnidadesAdministrativas: Array,
    Cargos: Array,
})

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)
const DTableRef = ref(null)

// Roles modal
const showRolesModal = ref(false)
const CargandoRoles = ref(false)
const rolesCatalogo = ref([])
const rolesSeleccionados = ref([])
const rolFiltro = ref('')

const usuarioRolesTarget = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,

    name: '',
    nombre: '',
    apellido_paterno: '',
    apellido_materno: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',

    telefono: '',
    extension: '',
    activo: true,
    unidad_administrativa_id: null,
    cargo_id: null,
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

const cargoNombreById = (id) => {
    const c = (props.Cargos ?? []).find((x) => x.id === id)
    return c ? c.nombre : '—'
}

// =============================== FUNCIONES  ===============================
const ReloadTable = () => DTableRef.value?.reload?.()

const ResetForm = () => {
    form.reset()
    form.id = null
    form.activo = true
    form.unidad_administrativa_id = null
    form.cargo_id = null
}

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return

        form.id = item.id
        form.name = item.name ?? ''
        form.nombre = item.nombre ?? ''
        form.apellido_paterno = item.apellido_paterno ?? ''
        form.apellido_materno = item.apellido_materno ?? ''
        form.username = item.username ?? ''
        form.email = item.email ?? ''

        form.password = ''
        form.password_confirmation = ''

        const d = item.Dato ?? item.dato ?? null
        form.telefono = d?.telefono ?? ''
        form.extension = d?.extension ?? ''
        form.activo = (d?.activo ?? true) ? true : false
        form.unidad_administrativa_id = d?.unidad_administrativa_id ?? null
        form.cargo_id = d?.cargo_id ?? null
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

    customConfirmSwal({
        title: '¿Está segur@ que desea eliminar este usuario?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
    }).then((result) => {
        if (!result.isConfirmed) return

        form.delete(route('usuarios.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                customToastSwal({ title: 'Usuario eliminado', icon: 'success' })
                ReloadTable()
            },
            onError: () => errorToast('Ocurrió un error'),
        })
    })
}

const onInvalidForm = () => warningToast('Revisa los campos marcados')

const ReiniciarPassword = (userId) => {
    if (!canUpdate.value) return

    customConfirmSwal({
        title: '¿Reiniciar contraseña?',
        text: 'La contraseña será igual al username del usuario.',
        icon: 'warning',
        confirmButtonText: 'Sí, reiniciar',
        cancelButtonText: 'Cancelar',
    }).then(async (result) => {
        if (!result.isConfirmed) return

        try {
            await axios.post(route('usuarios.ReiniciarPassword'), { id: userId })
            customToastSwal({ title: 'Contraseña reiniciada', icon: 'success' })
        } catch (e) {
            errorToast('Ocurrió un error al reiniciar la contraseña')
        }
    })
}

const AbrirModalRoles = async (item) => {
    if (!canUpdate.value) return

    usuarioRolesTarget.value = item
    rolesSeleccionados.value = []

    // Si el backend ya manda roles, intenta precargar (acepta varias formas)
    const roles =
        item?.roles ??
        item?.Roles ??
        item?.role_names ??
        item?.roleNames ??
        item?.roles_names ??
        null

    if (Array.isArray(roles)) rolesSeleccionados.value = roles.map(String)

    showRolesModal.value = true

    if (rolesCatalogo.value.length) return // ya cargado

    CargandoRoles.value = true
    try {
        const { data } = await axios.post(route('RolesPermisos.ConsultarRolesPermisos'))
        rolesCatalogo.value = Array.isArray(data) ? data : []
    } catch (e) {
        errorToast('No se pudieron cargar roles/permisos')
        rolesCatalogo.value = []
    } finally {
        CargandoRoles.value = false
    }
}

const RolesFiltrados = computed(() => {
    const q = (rolFiltro.value ?? '').trim().toLowerCase()
    if (!q) return rolesCatalogo.value
    return rolesCatalogo.value.filter((r) => (r?.name ?? '').toLowerCase().includes(q))
})

const ToggleRol = (roleName) => {
    const i = rolesSeleccionados.value.indexOf(roleName)
    if (i >= 0) rolesSeleccionados.value.splice(i, 1)
    else rolesSeleccionados.value.push(roleName)
}

const GuardarRoles = async () => {
    if (!canUpdate.value) return
    if (!usuarioRolesTarget.value?.id) return

    try {
        await axios.post(route('usuarios.AsignarRoles'), {
            user_id: usuarioRolesTarget.value.id,
            roles: rolesSeleccionados.value, // syncRoles: asigna y quita
        })

        customToastSwal({ title: 'Roles actualizados', icon: 'success' })
        showRolesModal.value = false
        ReloadTable()
    } catch (e) {
        errorToast('Ocurrió un error al asignar roles')
    }
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
                    {{ cargoNombreById((item.Dato ?? item.dato)?.cargo_id) }}
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
                    <div class="flex gap-2" v-if="canUpdate || canDelete">
                        <!-- Editar -->
                        <VButton
                            v-if="canUpdate"
                            size="x-small"
                            variant="flat"
                            color="teal-darken-1"
                            icon="mdi-pencil-outline"
                            @click="ChangeModal(item)"
                        />

                        <!-- Asignar Roles -->
                        <VButton
                            v-if="canUpdate"
                            size="x-small"
                            variant="flat"
                            color="indigo-darken-1"
                            icon="mdi-shield-account-outline"
                            title="Asignar roles"
                            @click="AbrirModalRoles(item)"
                        />

                        <!-- Reset Password -->
                        <VButton
                            v-if="canUpdate"
                            size="x-small"
                            variant="flat"
                            color="orange-darken-2"
                            icon="mdi-lock-reset"
                            title="Reiniciar contraseña"
                            @click="ReiniciarPassword(item.id)"
                        />

                        <!-- Eliminar -->
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

        <!-- ================================================ MODALES ================================================ -->
        <VDialog
            v-model="showModal"
            :title="form.id ? 'Editar usuario' : 'Nuevo usuario'"
            header-icon="mdi-account-cog-outline"
            max-width="800"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar" @invalid="onInvalidForm">
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        <!-- users: name + nombres -->
                        <div class="md:col-span-2">
                            <MdTextInput
                                v-model="form.name"
                                label="Nombre (display)"
                                icon="mdi-account-outline"
                                :required="true"
                                :minLength="3"
                                :maxLength="150"
                                counter
                            />
                        </div>

                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            icon="mdi-account-badge-outline"
                            :maxLength="100"
                            counter
                        />

                        <MdTextInput
                            v-model="form.apellido_paterno"
                            label="Apellido paterno"
                            icon="mdi-account-badge-outline"
                            :maxLength="100"
                            counter
                        />

                        <MdTextInput
                            v-model="form.apellido_materno"
                            label="Apellido materno"
                            icon="mdi-account-badge-outline"
                            :maxLength="100"
                            counter
                        />

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
                                :items="Cargos ? UnidadesAdministrativas : UnidadesAdministrativas"
                                item-value="id"
                                item-title="nombre"
                                clearable
                            />
                        </div>

                        <div class="md:col-span-2">
                            <MdSelect
                                v-model="form.cargo_id"
                                label="Cargo"
                                icon="mdi-briefcase-outline"
                                :items="Cargos"
                                item-value="id"
                                item-title="nombre"
                                clearable
                            />
                        </div>

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

        <!-- MODAL ROLES -->
        <VDialog
            v-model="showRolesModal"
            title="Asignar roles"
            header-icon="mdi-shield-account-outline"
            max-width="900"
        >
            <template #content>
                <div class="space-y-3">
                    <div class="text-sm text-gray-600">
                        Usuario: <b>{{ usuarioRolesTarget?.name }}</b>
                        <span v-if="usuarioRolesTarget?.username"> ({{ usuarioRolesTarget?.username }})</span>
                    </div>

                    <MdTextInput
                        v-model="rolFiltro"
                        label="Filtrar roles"
                        icon="mdi-magnify"
                        :maxLength="60"
                    />

                    <div v-if="CargandoRoles" class="text-sm text-gray-500">
                        Cargando roles y permisos...
                    </div>

                    <div v-else class="overflow-auto border rounded">
                        <div
                            v-for="r in RolesFiltrados"
                            :key="r.id"
                            class="pb-2 mb-2 transition border rounded"
                            :class="rolesSeleccionados.includes(r.name)
                                ? 'border-green-400 bg-green-50'
                                : 'border-gray-200 bg-white'"
                        >
                            <div class="flex items-center justify-between mx-2">
                                <label class="flex items-center cursor-pointer">
                                    <MdCheckbox
                                        color="customPrimary"
                                        :model-value="rolesSeleccionados.includes(r.name)"
                                        :label="r.name"
                                        :name="`role_${r.id}`"
                                        :show-success-state="false"
                                        @update:modelValue="(v) => {
                                            const has = rolesSeleccionados.includes(r.name)
                                            if (v && !has) rolesSeleccionados.push(r.name)
                                            if (!v && has) rolesSeleccionados.splice(rolesSeleccionados.indexOf(r.name), 1)
                                        }"
                                    />
                                </label>

                                <span class="text-xs text-gray-500">
                                    {{ (r.permissions ?? []).length }} permisos
                                </span>
                            </div>

                            <div class="grid grid-cols-1 gap-1 mx-2 mt-2 md:grid-cols-2">
                                <span
                                    v-for="p in (r.permissions ?? [])"
                                    :key="p.id"
                                    class="px-2 py-1 text-xs border rounded"
                                    :class="rolesSeleccionados.includes(r.name)
                                        ? 'bg-green-100 border-green-300 text-green-800'
                                        : 'bg-gray-50 border-gray-200'"
                                >
                                    {{ p.name }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <template #footer="{ close }">
                <VBtnCancel prepend-icon="mdi-close" @click="close">
                    Cerrar
                </VBtnCancel>

                <VBtnSend
                    prepend-icon="mdi-content-save-outline"
                    :disabled="CargandoRoles"
                    @click="GuardarRoles"
                >
                    Asignar roles
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
