<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import VButton from '@/Components/Vuetify/VButton.vue'
import DataTableServer from '@/Components/DataTableServer.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import FormValidate from '@/Components/FormValidate.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import VBtnSend from '@/Components/Vuetify/VBtnSend.vue'
import { customToastSwal, warningToast, errorToast } from '@/utils/swal'
import { customConfirmSwal } from '@/utils/swal'
// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? [])
const canCreate = computed(() => can.value.includes('cargos.store'))
const canUpdate = computed(() => can.value.includes('cargos.update'))
const canDelete = computed(() => can.value.includes('cargos.destroy'))

// =============================== PROPS ===============================
defineProps({
    Cargos: Object,
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
})

// =============================== TABLE ===============================
const headers = [
    { title: 'ID', key: 'id', sortable: true },
    { title: 'Nombre', key: 'nombre', sortable: true },
    { title: 'Acciones', key: 'actions', sortable: false },
]

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
    } else {
        if (!canCreate.value) return
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
                title: form.id
                    ? 'Cargo actualizado correctamente'
                    : 'Cargo registrado correctamente',
                icon: 'success',
            })
            showModal.value = false
            form.reset()
            ReloadTable()
        },
        onError: () => errorToast('Ocurrió un error'),
    }

    if (form.id) {
        form.put(route('cargos.update', form.id), options)
    } else {
        form.post(route('cargos.store'), options)
    }
}

const Eliminar = (id) => {
    customConfirmSwal({
        title: "¿Está segur@ que desea eliminar este registro?",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('cargos.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    customToastSwal({
                        title: 'Cargo eliminado',
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
    <AppLayout title="Cargos">
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
                title="Catálogo de Cargos"
                searchable
                search-label="Buscar cargo"
                search-placeholder="Escribe el nombre..."
                server-route="cargos.index"
                server-prop="Cargos"
                :headers="headers"
                :items-per-page="10"
            >
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
            :title="form.id ? 'Editar cargo' : 'Nuevo cargo'"
            header-icon="mdi-briefcase-outline"
            max-width="500"
        >
            <template #content>
                <FormValidate
                    ref="formValidateRef"
                    @submit="GuardarModificar"
                    @invalid="onInvalidForm"
                >
                    <MdTextInput
                        v-model="form.nombre"
                        label="Nombre"
                        :required="true"
                        :minLength="3"
                        :maxLength="150"
                        counter
                    />
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
