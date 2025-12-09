
<script setup>
import { onMounted, reactive, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import VButton from '@/Components/VButton.vue';
import DataTableServer from '@/Components/DataTableServer.vue';
import VDialog from '@/Components/VDialog.vue';
import FormValidate from '@/Components/FormValidate.vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';
import MdEmailInput from '@/Components/MaterialDesign/MdEmailInput.vue';
import MdPhoneInput from '@/Components/MaterialDesign/MdPhoneInput.vue';
import MdSelect from '@/Components/MaterialDesign/MdSelect.vue';
import MdSelectSearch from '@/Components/MaterialDesign/MdSelectSearch.vue';
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue';
import MdDateInput from '@/Components/MaterialDesign/MdDateInput.vue';
import MdSlider from '@/Components/MaterialDesign/MdSlider.vue';
import MdCheckbox from '@/Components/MaterialDesign/MdCheckbox.vue';
import MdSwitch from '@/Components/MaterialDesign/MdSwitch.vue';
import { MdRichText, MdTextarea } from '@/Components/MaterialDesign';
import MdFileInput from '@/Components/MaterialDesign/MdFileInput.vue';
import MdUploadArea from '@/Components/MaterialDesign/MdUploadArea.vue';
import Stepper from '@/Components/Stepper.vue';
import VBtnCancel from '@/Components/VBtnCancel.vue';
import VBtnSend from '@/Components/VBtnSend.vue';
import { customToastSwal, warningToast, errorToast } from "@/utils/swal";
import { useForm } from "@inertiajs/vue3";

// =============================== PROPS  ===============================
const props = defineProps({
    Examples: Object,
    Categorias: Object,
    Cocinas: Object,
})


// =============================== VARIABLES  ===============================
const showModal = ref(false);
const editMode = ref(false);

const step = ref(1);
const steps = [
    { title: 'Datos generales', value: 1 },
    { title: 'Detalles de preparación', value: 2 },
    { title: 'Contenido y archivos', value: 3 },
];

const formValidateRef = ref(null);
const form = useForm({
    id: null,
    nombre_receta: "Tacos de Dragón Supremo",
    chef_autor: "Maestro Flamel",
    codigo_receta: "12AB34",
    correo_contacto: "dragonchef@cocina.com",
    telefono_contacto: "2221234567",
    categoria: "Carnes",
    cocina_id: 1,
    porciones: 4,
    fecha_publicacion: "2025-02-01",
    nivel_picante: 85,
    es_vegetariana: false,
    requiere_horno: true,
    descripcion_breve: "Una receta ancestral de sabor intenso y fuego místico.",
    preparacion_html: "<p>Dora la carne de dragón, agrega especias arcanas y sirve caliente.</p>",
    foto_principal_path: null,
    galeria_imagenes_path: [],
});

const headers = [
    { title: 'Id',              key: 'id',                      sortable: true },
    { title: 'Nombre',          key: 'nombre_receta',           sortable: true },
    { title: 'Chef',            key: 'chef_autor',              sortable: true },
    { title: 'Correo',          key: 'correo_contacto',         sortable: true },
    { title: 'Telefono',        key: 'telefono_contacto',        sortable: true },
    { title: 'Categoria',           key: 'categoria',            sortable: true },
    { title: 'Acciones',        key: 'actions',                 sortable: false },
];

onMounted(() => {

});

// ================================ METODOS  ===============================
const ChangeModal = () => {
    showModal.value = !showModal.value;
};

const GuardarModificar = () => {
    const options = {
        onSuccess: () => {
            customToastSwal({
                title: editMode.value
                    ? 'Registro actualizado correctamente'
                    : 'Registro creado correctamente',
                icon: 'success',
            });
            showModal.value = false;
        },
        onError: () => {
            errorToast('Ocurrio un error');
            showModal.value = true;
        },
    };

    if (!!form.id) {
        form.put(route('Example.update', form.id), options);
    } else {
        form.post(route('Example.store'), options);
    }
};

const onInvalidForm = () => {
    warningToast('Revisa los campos marcados');
};
</script>

<template >
    <AppLayout title="Ejemplos">
        <template #actions>
            <VButton prepend-icon="mdi-plus" @click="ChangeModal()">Nuevo Registro</VButton>
        </template>

        <section>
            <DataTableServer
                title="Tabla de Ejemplos"
                searchable
                search-label="Buscar ..."
                search-placeholder="Escribe datos a buscar..."
                server-route="Example.index"
                server-prop="Examples"
                :headers="headers"
                :items-per-page="5"
                :items-per-page-options="[5, 10, 15, 20]"
            >
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn
                        size="x-small"
                        variant="text"
                        icon="mdi-eye"
                        @click="console.log('Ver hechizo', item?.raw?.id)"
                    />
                </template>
            </DataTableServer>
        </section>


        <VDialog v-model="showModal"  :max-width="1200">
                <template #header="{ close }">
                    <v-toolbar color="#0f766e" dark density="comfortable">
                        <v-toolbar-title>Editar empleado</v-toolbar-title>
                            <v-spacer />
                        <v-btn icon variant="text" @click="close">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>
                </template>

                <template #content>
                    <FormValidate
                        ref="formValidateRef"
                        @submit="GuardarModificar"
                        @invalid="onInvalidForm">
                            <Stepper v-model="step" :items="steps" mobile>
                                <template #item="{ step: currentStep }">
                                    <div>
                                        <div class="mb-6 font-semibold text-center text-pink-700 uppercase border-b-2 border-pink-700 text-h6">
                                            {{ currentStep.title }}
                                        </div>

                                        <!-- PASO 1: Datos generales -->
                                        <div v-if="currentStep.value === 1" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <MdTextInput
                                                v-model="form.nombre_receta"
                                                label="Nombre de la receta"
                                                icon="mdi-auto-fix"
                                                helper="Solo letras y espacios"
                                                :minLength="3"
                                                :maxLength="40"
                                                :required="true"
                                                :counter="true"
                                                :external-error="form.errors.correo_contacto"
                                            />

                                            <MdTextInput
                                                v-model="form.codigo_receta"
                                                label="Código de la receta"
                                                icon="mdi-code-braces"
                                                pattern="^[0-9]{2}[A-Za-z]{2}[0-9]{2}$"
                                                helper="Formato: 2 números, 2 letras, 2 números (Ej: 12AB34)"
                                                :uppercase="true"
                                                :minLength="6"
                                                :maxLength="6"
                                                :required="true"
                                                :counter="true"

                                            />

                                            <MdTextInput
                                                v-model="form.chef_autor"
                                                label="Chef autor"
                                                icon="mdi-chef-hat"
                                                helper="Solo letras y espacios"
                                                :minLength="3"
                                                :maxLength="40"
                                                :counter="true"
                                                :required="true"
                                            />

                                            <MdEmailInput
                                                v-model="form.correo_contacto"
                                                label="Correo de contacto"
                                                icon="mdi-email"
                                                helper="Ej: chef@cocina.com"
                                                :uppercase="false"
                                                :minLength="3"
                                                :maxLength="40"
                                                :counter="true"
                                                :required="true"
                                            />

                                            <MdPhoneInput
                                                v-model="form.telefono_contacto"
                                                label="Teléfono de contacto"
                                                helper="Ej: 2221234567"
                                                :required="true"
                                            />

                                            <MdSelect
                                                v-model="form.categoria"
                                                label="Categoría de cocina"
                                                :items="Categorias"
                                                item-title="nombre"
                                                item-value="nombre"
                                                :required="true"
                                            />

                                            <MdSelectSearch
                                                v-model="form.cocina_id"
                                                label="Tipos de cocina"
                                                helper="Seleccione una cocina"
                                                :items="Cocinas"
                                                item-title="nombre"
                                                item-value="id"
                                                chips
                                                :minSelected="1"
                                                :maxSelected="1"
                                                :required="true"
                                            />

                                            <MdNumberInput
                                                v-model="form.porciones"
                                                label="Porciones"
                                                icon="mdi-star-circle-outline"
                                                :allowed="'number'"
                                                :min="1"
                                                :max="9"
                                                :required="true"
                                                helper="Del 1 al 9"
                                            />
                                        </div>

                                        <!-- PASO 2: Detalles -->
                                        <div v-else-if="currentStep.value === 2" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <MdDateInput
                                                v-model="form.fecha_publicacion"
                                                label="Fecha de publicación"
                                                :required="true"
                                            />

                                            <MdSlider
                                                v-model="form.nivel_picante"
                                                label="Nivel picante"
                                                :min="0"
                                                :max="100"
                                                thumb-label="always"
                                                :required="true"
                                                color="secondary"
                                                :show-ticks="true"
                                            />

                                            <MdCheckbox
                                                v-model="form.es_vegetariana"
                                                label="¿Es vegetariana?"
                                                :required="true"
                                                color="#6d28d9"
                                            />

                                            <MdSwitch
                                                v-model="form.requiere_horno"
                                                label="¿Requiere horno?"
                                                :required="false"
                                                name="requiere_horno"
                                            />

                                            <MdTextarea
                                                v-model="form.descripcion_breve"
                                                label="Descripción receta"
                                                :required="true"
                                                :minLength="10"
                                                :maxLength="120"
                                                helper="Escribe una breve descripción"
                                                counter
                                                class="md:col-span-3"
                                            />

                                            <MdRichText
                                                v-model="form.preparacion_html"
                                                label="Preparación receta"
                                                :required="true"
                                                :minLength="10"
                                                :maxLength="300"
                                                toolbar="essential"
                                                helper="Describe la receta o conjuro"
                                                placeholder="Escribe la preparación..."
                                                class="md:col-span-3"
                                            />
                                        </div>

                                        <!-- PASO 3: Contenido y archivos -->
                                        <div v-else-if="currentStep.value === 3" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                            <MdFileInput
                                                class="md:col-span-3"
                                                v-model="form.foto_principal_path"
                                                label="Foto principal"
                                                chips
                                                counter
                                                accept=".png,.jpg,.jpeg,image/*"
                                                :required="true"
                                                :max-size-m-b="2"
                                                :external-error="form.errors.foto_principal_path"
                                            />

                                            <MdUploadArea
                                                class="md:col-span-3"
                                                v-model="form.galeria_imagenes_path"
                                                label="Galería de imágenes"
                                                description="Puedes subir hasta 5 imágenes"
                                                multiple
                                                accept="image/*"
                                                :required="true"
                                                :max-size-m-b="5"
                                                :max-files="5"
                                                :external-error="form.errors.foto_principal_path"
                                            />

                                        </div>
                                    </div>
                                </template>

                                <template #actions="{ prev, next }">
                                    <VButton prepend-icon="mdi-arrow-left" density="compact" @click="prev" :disabled="step === 1">Atras</VButton>
                                    <VButton prepend-icon="mdi-arrow-right" density="compact" @click="next" :disabled="step === 3">Siguiente</VButton>
                                </template>
                            </Stepper>
                    </FormValidate>
                </template>

                <template #footer="{ close }" >
                    <VBtnCancel prepend-icon="mdi-close"  @click="close">Cancel</VBtnCancel>

                    <VBtnSend prepend-icon="mdi-plus"  @click="formValidateRef?.submit()">
                        Guardar
                    </VBtnSend>
                </template>
        </VDialog>
    </AppLayout>
</template>
