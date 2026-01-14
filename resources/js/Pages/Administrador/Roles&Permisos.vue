<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

import AppLayout from '@/Layouts/AppLayout.vue'

import VButton from '@/Components/Vuetify/VButton.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'

import { customToastSwal, warningToast, errorToast } from '@/utils/swal'

// =============================== PROPS ===============================
const props = defineProps({
    Roles: { type: Array, default: () => [] },
    Permisos: { type: Array, default: () => [] },
})

// =============================== PERMISOS (SIAM) ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('RolesPermisos.store'))
const canUpdate = computed(() => can.value.includes('RolesPermisos.update'))
const canDelete = computed(() => can.value.includes('RolesPermisos.destroy'))


const showModal = ref(false)
const modalType = ref('role') // role | permission
const editMode = ref(false)
const loading = ref(false)

const IdRol = ref(null)
const PermisosAsignados = ref([])
const selectedPermissions = ref([])

const searchRole = ref('')
const searchPermission = ref('')
const rolesFormRef = ref(null)

// =============================== FORM ===============================
const form = useForm({
    id: null,
    name: '',
    Vista: 1,
})

const norm = (v) => (v ?? '').toString().trim().toLowerCase()

// =============================== MOOUNTED ===============================
onMounted(() => {
    console.log(can.value);
})


// =============================== MODAL ===============================
const OpenModal = (tipo = 1) => {
    if (!canCreate.value) return

    form.clearErrors()
    editMode.value = false
    form.reset('id', 'name')

    form.Vista = tipo
    modalType.value = tipo === 1 ? 'role' : 'permission'
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editMode.value = false
    form.reset('id', 'name')
    form.clearErrors()
}

const onInvalidRolesForm = () => warningToast('Revisa los campos marcados')

// ===============================  PERMISOS ASIGNADOS ===============================
const RolSeleccionado = async (id) => {
    if (IdRol.value === id) return

    IdRol.value = id
    loading.value = true

    try {
        const response = await axios.post(route('RolesPermisos.PermisosAsigados'), { id })
        PermisosAsignados.value = response.data || []
        selectedPermissions.value = PermisosAsignados.value.map((p) => p.id)
    } catch (error) {
        console.error(error)
        warningToast('No se pudieron cargar permisos asignados')
        PermisosAsignados.value = []
        selectedPermissions.value = []
    } finally {
        loading.value = false
    }
}

const togglePermiso = (permisoId) => {
    if (IdRol.value == null) {
        warningToast('Falta seleccionar un rol')
        return
    }

    const idx = selectedPermissions.value.indexOf(permisoId)
    if (idx >= 0) selectedPermissions.value.splice(idx, 1)
    else selectedPermissions.value.push(permisoId)
}

const selectAllFilteredPermissions = () => {
    if (IdRol.value == null) return
    const visibleIds = filteredPermisos.value.map((p) => p.id)
    const set = new Set(selectedPermissions.value)
    for (const id of visibleIds) set.add(id)
    selectedPermissions.value = Array.from(set)
}

const clearAllPermissions = () => {
    if (IdRol.value == null) return
    selectedPermissions.value = []
}

const GuardarPermisosRol = async () => {
    if (IdRol.value == null) {
        warningToast('Falta seleccionar un rol')
        return
    }

    if (!hasPermissionChanges.value) {
        warningToast('No hay cambios por guardar')
        return
    }

    loading.value = true

    try {
        const original = new Set((PermisosAsignados.value ?? []).map((p) => p.id))
        const current = new Set(selectedPermissions.value ?? [])

        const toAssign = []
        const toRemove = []

        for (const id of current) if (!original.has(id)) toAssign.push(id)
        for (const id of original) if (!current.has(id)) toRemove.push(id)

        const requests = []

        for (const permisoId of toAssign) {
            requests.push(
                axios.post(route('RolesPermisos.AsignarPermisos'), {
                    rol_id: IdRol.value,
                    permiso_id: permisoId,
                    assign: true,
                })
            )
        }

        for (const permisoId of toRemove) {
            requests.push(
                axios.post(route('RolesPermisos.AsignarPermisos'), {
                    rol_id: IdRol.value,
                    permiso_id: permisoId,
                    assign: false,
                })
            )
        }

        await Promise.all(requests)

        // snapshot
        PermisosAsignados.value = selectedPermissions.value.map((id) => ({ id }))
        customToastSwal({ title: 'Permisos guardados', icon: 'success' })
    } catch (error) {
        console.error(error)
        errorToast('No se pudieron guardar los permisos')
    } finally {
        loading.value = false
    }
}

// =============================== CRUD ROLES/PERMISOS ===============================
const GuardarModificar = () => {
    if (form.processing) return
    loading.value = true

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal()
            customToastSwal({
                title: editMode.value ? 'Registro actualizado' : 'Registro guardado',
                icon: 'success',
            })
        },
        onError: () => {
            errorToast('Ocurrió un error')
            showModal.value = true
        },
        onFinish: () => {
            loading.value = false
        },
    }

    // Update
    if (editMode.value && form.id) {
        if (!canUpdate.value) return
        form.put(route('RolesPermisos.update', { RolesPermiso: form.id }), options)
        return
    }

    // Create
    if (!canCreate.value) return
    form.post(route('RolesPermisos.store'), options)
}

const Editar = (data, tipo = 1) => {
    if (!canUpdate.value) return

    form.reset('id', 'name')
    form.clearErrors()

    editMode.value = true
    form.id = data?.id ?? null
    form.name = data?.name ?? ''

    form.Vista = tipo
    modalType.value = tipo === 1 ? 'role' : 'permission'
    showModal.value = true
}

const Eliminar = (data) => {
    if (!canDelete.value) return
    if (!confirm('¿Estás seguro de que deseas eliminar este registro?')) return

    loading.value = true

    form.delete(route('RolesPermisos.destroy', data.id), {
        preserveScroll: true,
        onSuccess: () => customToastSwal({ title: 'Eliminación exitosa', icon: 'success' }),
        onError: () => errorToast('Error al eliminar'),
        onFinish: () => (loading.value = false),
    })
}


// =============================== FILTROS (COMPUTRED) ===============================
const filteredRoles = computed(() => {
    const q = norm(searchRole.value)
    if (!q) return props.Roles
    return props.Roles.filter((role) => {
        const name = norm(role?.name)
        const id = (role?.id ?? '').toString()
        return name.includes(q) || id.includes(q)
    })
})

const filteredPermisos = computed(() => {
    const q = norm(searchPermission.value)
    if (!q) return props.Permisos
    return props.Permisos.filter((permiso) => {
        const name = norm(permiso?.name)
        const id = (permiso?.id ?? '').toString()
        return name.includes(q) || id.includes(q)
    })
})

const hasPermissionChanges = computed(() => {
    if (IdRol.value == null) return false
    const a = new Set((PermisosAsignados.value ?? []).map((p) => p.id))
    const b = new Set(selectedPermissions.value ?? [])
    if (a.size !== b.size) return true
    for (const id of a) if (!b.has(id)) return true
    return false
})

// =============================== WATCHERS ===============================
watch(IdRol, () => {
    searchPermission.value = ''
})
</script>

<template>
    <AppLayout title="Roles y Permisos">
        <template #actions>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <VButton prepend-icon="mdi-account-plus-outline" @click="OpenModal(1)">
                    Agregar Rol
                </VButton>

                <VButton prepend-icon="mdi-key-plus" @click="OpenModal(2)">
                    Agregar Permiso
                </VButton>
            </div>
        </template>

        <section>
            <!-- Info -->
            <div class="mb-4">
                <div class="px-4 py-3 text-sm font-medium border md:text-base text-slate-700 bg-slate-50 rounded-xl border-slate-200">
                    <div class="flex items-start gap-2">
                        <i class="mt-1 fa-solid fa-circle-info text-slate-500"></i>
                        <p class="leading-relaxed">
                            Selecciona un rol y asigna los permisos desde la lista derecha.
                            <span class="text-slate-600">
                                Los cambios no se aplican hasta presionar <span class="font-semibold">Guardar permisos</span>.
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="h-[80vh] md:h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                    <!-- ROLES PANEL -->
                    <div class="h-[73vh] overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-200 text-slate-800">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold tracking-wide uppercase text-app-primary">
                                        Roles
                                    </h3>
                                    <p class="text-xs text-app-primary/90 mt-0.5">
                                        {{ filteredRoles?.length ?? 0 }} roles
                                    </p>
                                </div>

                                <div class="w-4/6">
                                    <MdTextInput
                                        v-model="searchRole"
                                        label="Buscar rol"
                                        icon="mdi-magnify"
                                        :uppercase="false"
                                        clearable
                                        helper="Escribe parte del nombre del rol"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="h-[70vh] overflow-y-auto p-3">
                            <div v-if="!filteredRoles?.length" class="flex items-center justify-center h-full">
                                <div class="text-center text-slate-600">
                                    <i class="text-3xl mdi mdi-account-search-outline"></i>
                                    <div class="mt-2 text-sm">No hay roles que coincidan.</div>
                                </div>
                            </div>

                            <div v-else class="flex flex-col gap-2 px-2 py-4">
                                <button
                                    v-for="role in filteredRoles"
                                    :key="role.id"
                                    type="button"
                                    class="w-full px-3 py-2 text-left transition duration-150 ease-in-out border rounded-xl"
                                    :class="IdRol === role.id
                                        ? 'bg-app-primary/10 border-app-primary'
                                        : 'bg-white border-slate-200 hover:border-app-primary/70 hover:shadow-sm'"
                                    @click="RolSeleccionado(role.id)"
                                >
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="text-[10px] px-2 py-0.5 rounded-full border bg-slate-50"
                                                    :class="IdRol === role.id
                                                        ? 'border-app-primary/40 text-app-primary'
                                                        : 'border-slate-300'"
                                                >
                                                    #{{ role.id }}
                                                </span>

                                                <span
                                                    class="truncate"
                                                    :class="IdRol === role.id
                                                        ? 'font-semibold text-app-primary'
                                                        : 'font-medium text-slate-800'"
                                                >
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="shrink-0 opacity-90">
                                            <i v-if="IdRol === role.id" class="mdi mdi-check-circle text-app-primary"></i>
                                            <i v-else class="mdi mdi-chevron-right text-slate-400"></i>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PERMISOS PANEL -->
                    <div class="h-[73vh] overflow-hidden bg-white border shadow-sm rounded-2xl border-slate-200">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-semibold tracking-wide uppercase text-app-primary">
                                        Permisos
                                    </h3>

                                    <div class="text-xs text-slate-600 mt-0.5 flex flex-wrap items-center gap-2">
                                        <span>{{ filteredPermisos?.length ?? 0 }} permisos</span>

                                        <span v-if="IdRol" class="text-slate-400">•</span>

                                        <span v-if="IdRol">
                                            Seleccionados:
                                            <span class="font-semibold text-app-primary">
                                                {{ selectedPermissions?.length ?? 0 }}
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="w-4/6">
                                    <MdTextInput
                                        v-model="searchPermission"
                                        label="Buscar permiso"
                                        icon="mdi-magnify"
                                        :uppercase="false"
                                        clearable
                                        :readonly="!IdRol"
                                        helper="Filtra por nombre del permiso"
                                    />
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 mt-2">
                                <div class="flex items-center gap-2">
                                    <VButton
                                        variant="tonal"
                                        prepend-icon="mdi-checkbox-multiple-marked-outline"
                                        :disabled="!IdRol || !(filteredPermisos?.length)"
                                        @click="selectAllFilteredPermissions()"
                                    >
                                        Seleccionar visibles
                                    </VButton>

                                    <VButton
                                        variant="tonal"
                                        prepend-icon="mdi-checkbox-multiple-blank-outline"
                                        :disabled="!IdRol || !(selectedPermissions?.length)"
                                        @click="clearAllPermissions()"
                                    >
                                        Limpiar
                                    </VButton>
                                </div>

                                <div v-if="IdRol" class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-1 text-xs border rounded-full"
                                        :class="hasPermissionChanges
                                            ? 'border-amber-300 bg-amber-50 text-amber-800'
                                            : 'border-emerald-200 bg-emerald-50 text-emerald-800'"
                                    >
                                        {{ hasPermissionChanges ? 'Cambios pendientes' : 'Todo guardado' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="h-[70vh] overflow-y-auto p-3">
                            <div v-if="!IdRol" class="flex items-center justify-center h-full">
                                <div class="max-w-sm text-center text-slate-600">
                                    <i class="text-4xl mdi mdi-account-arrow-right-outline"></i>
                                    <div class="mt-2 font-semibold text-slate-700">Selecciona un rol</div>
                                    <div class="mt-1 text-sm text-slate-500">
                                        Para ver y asignar permisos primero elige un rol en la columna izquierda.
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="!filteredPermisos?.length" class="flex items-center justify-center h-full">
                                <div class="text-center text-slate-600">
                                    <i class="text-3xl mdi mdi-shield-search-outline"></i>
                                    <div class="mt-2 text-sm">No hay permisos que coincidan.</div>
                                </div>
                            </div>

                            <div v-else class="flex flex-col gap-2 px-2 py-4">
                                <div
                                    v-for="permiso in filteredPermisos"
                                    :key="permiso.id"
                                    class="px-3 transition duration-150 ease-in-out border rounded-xl"
                                    :class="selectedPermissions.includes(permiso.id)
                                        ? 'bg-app-secondary border-app-primary'
                                        : 'bg-white border-slate-200 hover:border-app-primary/70 hover:shadow-sm'"
                                    role="button"
                                    tabindex="0"
                                    @click="togglePermiso(permiso.id)"
                                    @keydown.enter.prevent="togglePermiso(permiso.id)"
                                    @keydown.space.prevent="togglePermiso(permiso.id)"
                                >
                                    <div class="flex items-center gap-3">
                                        <v-checkbox
                                            :model-value="selectedPermissions.includes(permiso.id)"
                                            density="compact"
                                            hide-details
                                            class="shrink-0"
                                            @click.stop
                                            @update:model-value="() => togglePermiso(permiso.id)"
                                        />

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] px-2 py-0.5 rounded-full border border-slate-300 text-app-primary font-bold bg-slate-50">
                                                    #{{ permiso.id }}
                                                </span>
                                                <span
                                                    class="truncate"
                                                    :class="selectedPermissions.includes(permiso.id)
                                                        ? 'font-semibold text-white'
                                                        : 'font-medium text-slate-700'"
                                                >
                                                    {{ permiso.name }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="shrink-0 opacity-90">
                                            <i v-if="selectedPermissions.includes(permiso.id)" class="mdi mdi-check-circle text-app-primary"></i>
                                            <i v-else class="mdi mdi-circle-outline text-slate-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sticky bottom-0 px-4 py-3 border-t border-slate-200 bg-white/95 backdrop-blur">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <VBtnSend
                                    prepend-icon="mdi-content-save"
                                    :loading="loading"
                                    :disabled="loading || !IdRol || !hasPermissionChanges"
                                    @click="GuardarPermisosRol()"
                                >
                                    {{ hasPermissionChanges ? 'Guardar permisos' : 'Permisos guardados' }}
                                </VBtnSend>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MODAL ROLES/PERMISOS -->
        <VDialog
            v-model="showModal"
            :title="modalType === 'role'
                ? (editMode ? 'Editar Rol' : 'Nuevo Rol')
                : (editMode ? 'Editar Permiso' : 'Nuevo Permiso')"
            header-icon="mdi-shield-account-outline"
            max-width="650"
        >
            <template #content>
                <FormValidate
                    ref="rolesFormRef"
                    @submit="GuardarModificar"
                    @invalid="onInvalidRolesForm"
                >
                    <MdTextInput
                        v-model="form.name"
                        :label="modalType === 'role' ? 'Nombre del rol' : 'Nombre del permiso'"
                        icon="mdi-account-key-outline"
                        required
                        :uppercase="true"
                        :external-error="form.errors.name"
                        :helper="modalType === 'permission'
                            ? 'Ej: empleados.view / empleados.create'
                            : 'Ej: ADMINISTRADOR, RH, SUPERVISOR'"
                    />
                </FormValidate>
            </template>

            <template #footer="{ close }">
                <VBtnSend
                    prepend-icon="mdi-content-save"
                    @click="rolesFormRef?.submit()"
                    :loading="form.processing"
                    :disabled="form.processing"
                    class="me-2"
                >
                    {{ editMode ? 'Actualizar' : 'Registrar' }}
                </VBtnSend>

                <VBtnCancel prepend-icon="mdi-close" @click="close">
                    Cerrar
                </VBtnCancel>
            </template>
        </VDialog>
    </AppLayout>
</template>
