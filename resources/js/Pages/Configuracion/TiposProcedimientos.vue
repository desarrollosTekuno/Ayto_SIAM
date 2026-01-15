<!-- resources/js/Pages/Catalogos/TiposProcedimientos.vue -->
<script setup>
import { ref, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

import AppLayout from "@/Layouts/AppLayout.vue";
import DataTableServer from "@/Components/DataTableServer.vue";
import VDialog from "@/Components/Vuetify/VDialog.vue";
import VButton from "@/Components/Vuetify/VButton.vue";
import VBtnCancel from "@/Components/Vuetify/VBtnCancel.vue";
import VBtnSend from "@/Components/Vuetify/VBtnSend.vue";
import FormValidate from "@/Components/FormValidate.vue";

import MdTextInput from "@/Components/MaterialDesign/MdTextInput.vue";
import MdSelect from "@/Components/MaterialDesign/MdSelect.vue";

import { customConfirmSwal, customToastSwal } from "@/utils/swal";

// =============================== PERMISOS ===============================
const can = computed(() => usePage().props.auth?.permissions ?? []);
const canCreate = computed(() => can.value.includes("tipos_procedimientos.store"));
const canUpdate = computed(() => can.value.includes("tipos_procedimientos.update"));
const canDelete = computed(() => can.value.includes("tipos_procedimientos.destroy"));

// =============================== PROPS ===============================
const props = defineProps({
    TiposProcedimientos: Object,
});

const showModal = ref(false);
const formValidateRef = ref(null);
const DTableRef = ref(null);

// =============================== FORM ===============================
const form = useForm({
    id: null,
    clave: "",
    nombre: "",
    ambito: "",
    descripcion: "",
    activo: true,
});

// =============================== TABLE ===============================
const headers = [
    { title: "ID", key: "id", sortable: true },
    { title: "Clave", key: "clave", sortable: true },
    { title: "Nombre", key: "nombre", sortable: true },
    { title: "Ámbito", key: "ambito", sortable: true },
    { title: "Activo", key: "activo", sortable: false },
    { title: "Acciones", key: "actions", sortable: false },
];

// =============================== METHODS ===============================
const ReloadTable = () => {
    DTableRef.value?.reload?.();
};

const ResetForm = () => {
    form.reset();
    form.clearErrors();
    form.id = null;
    form.activo = true;
};

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return;

        form.id = item.id;
        form.clave = item.clave ?? "";
        form.nombre = item.nombre ?? "";
        form.ambito = item.ambito ?? "";
        form.descripcion = item.descripcion ?? "";
        form.activo = !!item.activo;
    } else {
        if (!canCreate.value) return;
        ResetForm();
    }

    showModal.value = true;
};

const GuardarModificar = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({
                title: form.id
                    ? "Tipo de procedimiento actualizado correctamente"
                    : "Tipo de procedimiento registrado correctamente",
                icon: "success",
            });
            showModal.value = false;
            ResetForm();
            ReloadTable();
        },
        onError: () => {
            customToastSwal({
                title: "Ocurrió un error al guardar",
                icon: "error",
            });
        },
    };

    if (form.id) {
        if (!canUpdate.value) return;
        form.put(route("tipos_procedimientos.update", form.id), options);
    } else {
        if (!canCreate.value) return;
        form.post(route("tipos_procedimientos.store"), options);
    }
};

const destroy = (item) => {
    if (!canDelete.value) return;

    customConfirmSwal({
        title: "¿Está segur@ que desea eliminar este registro?",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route("tipos_procedimientos.destroy", item.id), {
                onSuccess: () => {
                    customToastSwal({
                        title: "Registro eliminado correctamente",
                        icon: "success",
                    });
                    ReloadTable();
                },
                onError: () => {
                    customToastSwal({
                        title: "Error al eliminar el registro",
                        icon: "error",
                    });
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Tipos de Procedimientos">
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
                title="Catálogo de Tipos de Procedimientos"
                searchable
                search-label="Buscar tipo"
                search-placeholder="Clave o nombre..."
                server-route="tipos_procedimientos.index"
                server-prop="TiposProcedimientos"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.activo`]="{ item }">
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded"
                        :class="item.activo
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700'"
                    >
                        {{ item.activo ? "Activo" : "Inactivo" }}
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
                            @click="destroy(item)"
                        />
                    </div>
                </template>
            </DataTableServer>
        </section>

        <!-- MODAL -->
        <VDialog
            v-model="showModal"
            :title="form.id ? 'Editar tipo de procedimiento' : 'Nuevo tipo de procedimiento'"
            header-icon="mdi-file-document-outline"
            max-width="600"
        >
            <template #content>
                <FormValidate
                    ref="formValidateRef"
                    @submit="GuardarModificar"
                >
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <MdTextInput
                            v-model="form.clave"
                            label="Clave"
                            icon="mdi-identifier"
                            :required="true"
                            :maxLength="20"
                            counter
                            :external-error="form.errors.clave"
                        />

                        <MdTextInput
                            v-model="form.nombre"
                            label="Nombre"
                            icon="mdi-text"
                            :required="true"
                            :maxLength="150"
                            counter
                            :external-error="form.errors.nombre"
                        />

                        <MdTextInput
                            v-model="form.ambito"
                            label="Ámbito"
                            icon="mdi-map-outline"
                            :maxLength="20"
                            counter
                            :external-error="form.errors.ambito"
                        />

                        <MdTextInput
                            v-model="form.descripcion"
                            label="Descripción"
                            icon="mdi-text-box-outline"
                            :maxLength="255"
                            counter
                            :external-error="form.errors.descripcion"
                        />

                        <MdSelect
                            v-model="form.activo"
                            label="Estatus"
                            :items="[
                                { id: true, nombre: 'Activo' },
                                { id: false, nombre: 'Inactivo' },
                            ]"
                            item-title="nombre"
                            item-value="id"
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
                    {{ form.id ? "Actualizar" : "Guardar" }}
                </VBtnSend>
            </template>
        </VDialog>
    </AppLayout>
</template>
