
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
    </AppLayout>
</template>
