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
const canCreate = computed(() => can.value.includes("archivos_permitidos.store"));
const canUpdate = computed(() => can.value.includes("archivos_permitidos.update"));
const canDelete = computed(() => can.value.includes("archivos_permitidos.destroy"));

// =============================== PROPS ===============================
const props = defineProps({
    ArchivosPermitidos: Object,
    TiposProcedimientos: Array,
});

// =============================== STATE ===============================
const showModal = ref(false);
const formValidateRef = ref(null);
const DTableRef = ref(null);

// =============================== FORM ===============================
const form = useForm({
    id: null,
    extension: "",
    obligatorio: false,
    activo: true,
    tipo_procedimiento_id: null,
});

// =============================== TABLE ===============================
const headers = [
    { title: "ID", key: "id", sortable: true },
    { title: "Extensión", key: "extension", sortable: true },
    { title: "Tipo procedimiento", key: "tipo_procedimiento", sortable: false },
    { title: "Obligatorio", key: "obligatorio", sortable: false },
    { title: "Activo", key: "activo", sortable: false },
    { title: "Acciones", key: "actions", sortable: false },
];

// =============================== METHODS ===============================
const ReloadTable = () => DTableRef.value?.reload?.();

const ResetForm = () => {
    form.reset();
    form.clearErrors();
    form.id = null;
    form.obligatorio = false;
    form.activo = true;
    form.tipo_procedimiento_id = null;
};

/** Normaliza extensión: sin espacios, sin punto inicial, en minúsculas */
const normalizeExtension = (value) => {
    if (value == null) return "";
    return String(value).trim().replace(/^\./, "").toLowerCase();
};

const ChangeModal = (item = null) => {
    if (item) {
        if (!canUpdate.value) return;

        form.id = item.id;
        form.extension = item.extension ?? "";
        form.obligatorio = !!item.obligatorio;
        form.activo = !!item.activo;
        form.tipo_procedimiento_id = item.tipo_procedimiento_id ?? null;
    } else {
        if (!canCreate.value) return;
        ResetForm();
    }

    showModal.value = true;
};

const GuardarModificar = () => {
    form.extension = normalizeExtension(form.extension);

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            customToastSwal({
                title: form.id
                    ? "Archivo permitido actualizado correctamente"
                    : "Archivo permitido registrado correctamente",
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
        form.put(route("archivos_permitidos.update", form.id), options);
    } else {
        if (!canCreate.value) return;
        form.post(route("archivos_permitidos.store"), options);
    }
};

const destroy = (item) => {
    if (!canDelete.value) return;

    customConfirmSwal({
        title: "¿Está segur@ que desea eliminar este registro?",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route("archivos_permitidos.destroy", item.id), {
                onSuccess: () => {
                    customToastSwal({
                        title: "Registro eliminado correctamente",
                        icon: "success",
                    });
                    ReloadTable();
                },
                onError: (err) => {
                    console.error(err);
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
    <AppLayout title="Archivos permitidos">
        <template #actions>
            <VButton v-if="canCreate" prepend-icon="mdi-plus" @click="ChangeModal()">
                Nuevo Registro
            </VButton>
        </template>

        <!-- TABLA -->
        <section>
            <DataTableServer
                ref="DTableRef"
                title="Catálogo de Archivos Permitidos"
                searchable
                search-label="Buscar extensión"
                search-placeholder="Ej: pdf, docx, xlsx..."
                server-route="archivos_permitidos.index"
                server-prop="ArchivosPermitidos"
                :headers="headers"
                :items-per-page="10"
            >
                <template v-slot:[`item.extension`]="{ item }">
                    <span class="font-mono text-sm">.{{ item.extension }}</span>
                </template>

                <template v-slot:[`item.tipo_procedimiento`]="{ item }">
                    <span class="text-slate-400" v-if="!item?.tipo_procedimiento">—</span>
                    <span v-else>
                        <span class="font-mono text-xs text-slate-500">
                            {{ item.tipo_procedimiento?.clave }}
                        </span>
                        <span class="mx-1 text-slate-300">|</span>
                        <span>{{ item.tipo_procedimiento?.nombre }}</span>
                    </span>
                </template>

                <template v-slot:[`item.obligatorio`]="{ item }">
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded"
                        :class="item.obligatorio
                            ? 'bg-amber-100 text-amber-700'
                            : 'bg-slate-100 text-slate-600'"
                    >
                        {{ item.obligatorio ? "Sí" : "No" }}
                    </span>
                </template>

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
            :title="form.id ? 'Editar archivo permitido' : 'Nuevo archivo permitido'"
            header-icon="mdi-file-check-outline"
            max-width="650"
        >
            <template #content>
                <FormValidate ref="formValidateRef" @submit="GuardarModificar">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <MdSelect
                            v-model="form.tipo_procedimiento_id"
                            label="Tipo de procedimiento"
                            :items="TiposProcedimientos"
                            item-title="nombre"
                            item-value="id"
                            :required="true"
                        />

                        <MdTextInput
                            v-model="form.extension"
                            label="Extensión"
                            icon="mdi-file-outline"
                            :required="true"
                            :maxLength="10"
                            counter
                            helper="Sin punto. Ej: pdf"
                            :external-error="form.errors.extension"
                        />

                        <MdSelect
                            v-model="form.obligatorio"
                            label="¿Obligatorio?"
                            :items="[
                                { id: true, nombre: 'Sí' },
                                { id: false, nombre: 'No' },
                            ]"
                            item-title="nombre"
                            item-value="id"
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
