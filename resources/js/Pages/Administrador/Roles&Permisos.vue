<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

import AppLayout from '@/Layouts/AppLayout.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import VButton from '@/Components/Vuetify/VButton.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'

import { customToastSwal, warningToast, errorToast } from '@/utils/swal'

// =============================== PROPS ===============================
const props = defineProps({
    Roles: { type: Array, default: () => [] },
    Permisos: { type: Array, default: () => [] },
})

// =============================== PERMISOS UI (opcional) ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('roles_permisos.store'))
const canUpdate = computed(() => can.value.includes('roles_permisos.update'))
const canDelete = computed(() => can.value.includes('roles_permisos.destroy'))

// =============================== STATE ===============================
const showModal = ref(false)
const formValidateRef = ref(null)

const modalVista = ref(1) // 1=Rol, 2=Permiso
const isSubmitting = ref(false)

const searchRole = ref('')
const searchPermission = ref('')

const IdRol = ref(null)
const selectedPermissions = ref([]) // ids

// =============================== FORM ===============================
const form = useForm({
    id: null,
    name: '',
    Vista: 1, // 1 rol, 2 permiso (como tu proyecto anterior)
})

// =============================== COMPUTEDS (buscadores) ===============================
const filteredRoles = computed(() => {
    const q = searchRole.value.trim().toLowerCase()
    if (!q) return props.Roles
    return props.Roles.filter(r =>
        (r.name ?? '').toLowerCase().includes(q) || String(r.id).includes(q)
    )
})

const filteredPermisos = computed(() => {
    const q = searchPermission.value.trim().toLowerCase()
    if (!q) return props.Permisos
    return props.Permisos.filter(p =>
        (p.name ?? '').toLowerCase().includes(q) || String(p.id).includes(q)
    )
})

// =============================== MODAL ===============================
const OpenModal = (tipo) => {
    // tipo: 1 rol, 2 permiso
    if (!canCreate.value) return
    modalVista.value = tipo
    form.Vista = tipo
    form.id = null
    form.name = ''
    showModal.value = true
}

const CloseModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
}

// =============================== ROLES -> PERMISOS ASIGNADOS ===============================
const RolSeleccionado = async (id) => {
    IdRol.value = id
    selectedPermissions.value = []

    try {
        const { data } = await axios.post(route('roles_permisos.permisos_asignados'), { id })
        // Espera array de permisos [{id, name, ...}]
        selectedPermissions.value = (data ?? []).map(p => p.id)
    } catch (e) {
        errorToast('No se pudieron obtener los permisos asignados')
    }
}

const togglePermiso = async (permisoId) => {
    if (!IdRol.value) {
        warningToast('Falta seleccionar un rol')
        return
    }

    const isSelected = selectedPermissions.value.includes(permisoId)

    // UI optimista
    if (isSelected) {
        selectedPermissions.value = selectedPermissions.value.filter(id => id !== permisoId)
    } else {
        selectedPermissions.value.push(permisoId)
    }

    try {
        await axios.post(route('roles_permisos.asignar_permiso'), {
            rol_id: IdRol.value,
            permiso_id: permisoId,
            assign: !isSelected,
        })

        customToastSwal({ title: 'Permiso modificado', icon: 'success' })
    } catch (e) {
        // rollback si falla
        if (isSelected) selectedPermissions.value.push(permisoId)
        else selectedPermissions.value = selectedPermissions.value.filter(id => id !== permisoId)

        errorToast('No se pudo modificar el permiso')
    }
}

// =============================== CRUD ROLES/PERMISOS ===============================
const Guardar = () => {
    if (!canCreate.value) return

    isSubmitting.value = true
    form.post(route('roles_permisos.store'), {
        preserveScroll: true,
        onSuccess: () => {
            CloseModal()
            customToastSwal({ title: 'Registro exitoso', icon: 'success' })
        },
        onError: () => errorToast('Error al registrar'),
        onFinish: () => (isSubmitting.value = false),
    })
}

const onInvalidForm = () => warningToast('Revisa los campos marcados')
</script>

<template>
    <AppLayout title="Roles y Permisos">
        <template #actions>
            <div class="flex justify-end gap-2">
                <VButton v-if="canCreate" prepend-icon="mdi-account-plus-outline" @click="OpenModal(1)">
                    Agregar Rol
                </VButton>
                <VButton v-if="canCreate" prepend-icon="mdi-key-outline" @click="OpenModal(2)">
                    Agregar Permiso
                </VButton>
            </div>
        </template>

        <section class="my-2">
            <p class="px-4 py-2 mb-4 text-sm font-medium text-center rounded-lg shadow-sm text-slate-500 bg-slate-50">
                <i class="fa-solid fa-circle-info"></i>
                Selecciona un rol (izquierda) y marca permisos (derecha).
            </p>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- ROLES -->
                <div class="h-[70vh]">
                    <input
                        v-model="searchRole"
                        type="text"
                        placeholder="Buscar rol..."
                        class="w-full p-2 mb-2 border rounded-md border-slate-300 focus:ring focus:ring-slate-200"
                    />

                    <div class="h-[65vh] overflow-y-auto">
                        <div
                            v-for="role in filteredRoles"
                            :key="role.id"
                            class="flex items-center gap-3 p-2 mb-3 transition rounded-lg shadow cursor-pointer"
                            :class="IdRol === role.id ? 'bg-slate-800 text-white' : 'hover:bg-slate-100'"
                            @click="RolSeleccionado(role.id)"
                        >
                            <div class="text-xs opacity-80">{{ role.id }}:</div>
                            <div class="font-medium">{{ role.name }}</div>
                        </div>

                        <div v-if="!filteredRoles.length" class="py-10 text-center text-slate-500">
                            Sin roles.
                        </div>
                    </div>
                </div>

                <!-- PERMISOS -->
                <div class="h-[70vh]">
                    <input
                        v-model="searchPermission"
                        type="text"
                        placeholder="Buscar permiso..."
                        class="w-full p-2 mb-2 border rounded-md border-slate-300 focus:ring focus:ring-slate-200"
                    />

                    <div class="h-[65vh] overflow-y-auto">
                        <div
                            v-for="permiso in filteredPermisos"
                            :key="permiso.id"
                            class="flex items-center gap-3 p-2 mb-3 transition rounded-lg shadow cursor-pointer"
                            :class="selectedPermissions.includes(permiso.id) ? 'bg-slate-800 text-white' : 'hover:bg-slate-100'"
                            @click="togglePermiso(permiso.id)"
                        >
                            <input
                                type="checkbox"
                                class="w-4 h-4"
                                :checked="selectedPermissions.includes(permiso.id)"
                                @change.prevent
                            />
                            <div class="text-xs opacity-80">{{ permiso.id }}:</div>
                            <div class="font-medium">{{ permiso.name }}</div>
                        </div>

                        <div v-if="!filteredPermisos.length" class="py-10 text-center text-slate-500">
                            Sin permisos.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MODAL CREAR -->
        <VDialog
            v-model="showModal"
            :title="`Registrar ${modalVista === 1 ? 'Rol' : 'Permiso'}`"
            header-icon="mdi-shield-account-outline"
            max-width="600"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="Guardar" @invalid="onInvalidForm">
                    <MdTextInput
                        v-model="form.name"
                        :label="modalVista === 1 ? 'Nombre del Rol' : 'Nombre del Permiso'"
                        icon="mdi-form-textbox"
                        :required="true"
                        :external-error="form.errors.name"
                        :minLength="2"
                        :maxLength="100"
                        counter
                    />
                </FormValidate>
            </template>

            <template #footer="{ close }">
                <VBtnCancel prepend-icon="mdi-close" @click="close(); CloseModal()">
                    Cerrar
                </VBtnCancel>

                <VBtnSend
                    prepend-icon="mdi-content-save-outline"
                    :disabled="isSubmitting"
                    @click="formValidateRef?.submit()"
                >
                    Registrar
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
