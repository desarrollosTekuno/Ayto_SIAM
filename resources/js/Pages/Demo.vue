<template>
    <AppLayout>
        <template #header> Demo </template>

        <section class="flex justify-center py-4 mx-4">
            <form @submit.prevent="handleSubmit" class="flex flex-row flex-wrap w-full gap-4">

                <MdTextInput
                    :ref="setFieldRef('nombre')"
                    v-model="form.nombre"
                    label="Nombre"
                    icon="mdi-account"
                    :required="true"
                    clearable
                />

                <MdTextInput
                    :ref="setFieldRef('curp')"
                    v-model="form.curp"
                    label="CURP"
                    icon="mdi-card-account-details"
                    :required="true"
                    :minLength="18"
                    :maxLength="18"
                    allowed="alphanumeric"
                    :pattern="/^([A-ZÑ&]{4})(\d{2})(\d{2})(\d{2})([HM])/"
                    helper="18 caracteres, solo letras y números"
                />

                <MdNumberInput
                    :ref="setFieldRef('edad')"
                    v-model="form.edad"
                    label="Edad"
                    :allowed="'number'"
                    :min="18"
                    :max="99"
                    :maxLength="2"
                    :required="true"
                />

                <!-- Decimal / dinero -->
                <MdNumberInput
                    :ref="setFieldRef('monto')"
                    v-model="form.monto"
                    label="Monto"
                    icon="mdi-currency-usd"
                    :allowed="'money'"
                    :allowNegative="true"
                    :required="true"
                />

                <!-- Negativo permitido -->

                <MdNumberInput
                    :ref="setFieldRef('saldo')"
                    v-model="form.saldo"
                    label="Saldo"
                    :allowNegative="true"
                    :required="true"
                />

            <MdDateInput
                :ref="setFieldRef('fecha_nacimiento')"
                v-model="form.fecha_nacimiento"
                label="Fecha de nacimiento"
                :required="true"
            />

            <MdDateInput
                :ref="setFieldRef('fecha_cita')"
                v-model="form.fecha_cita"
                label="Fecha de cita"
                :range="true"
                :required="true"
            />

            <MdDatePicker
                :ref="setFieldRef('fecha_rango')"
                v-model="form.fecha_rango"
                label="Fecha de cita (rango)"
                :range="true"
                :required="true"
            />

            <MdDatePicker
                :ref="setFieldRef('fecha_simple')"
                v-model="form.fecha_simple"
                label="Fecha simple"
                :required="true"
            />

            <MdSelect
                :ref="setFieldRef('frutas_id')"
                v-model="form.frutas_id"
                label="Departamento"
                :items="Frutas"
                item-title="nombre"
                item-value="id"
                :required="true"
            />

            <MdSelectSearch
                :ref="setFieldRef('colores_id')"
                v-model="form.colores_id"
                :items="Colores"
                label="colores"
                multiple
                chips
                item-title="nombre"
                item-value="id"
                :minSelected="2"
                :maxSelected="3"
                :required="true"
            />

            <MdCheckbox
                :ref="setFieldRef('acepta_terminos')"
                v-model="form.acepta_terminos"
                label="Acepto términos y condiciones"
                color="orange-darken-3"
                :required="true"
            />

            <MdSwitch
                :ref="setFieldRef('activo')"
                v-model="form.activo"
                label="Activo"
                color="indigo-darken-3"
                :required="true"
            />

            <MdRadioGroup
                v-model="form.genero"
                :ref="setFieldRef('genero')"
                label="Genero"
                :items="[
                    { value: 'H', label: 'Hombre' },
                    { value: 'M', label: 'Mujer' }
                ]"
                :required="true"
                color="orange-darken-3"
                inline
            />

            <MdTextarea
                :ref="setFieldRef('comentarios')"
                v-model="form.comentarios"
                label="Comentarios"
                :required="true"
                :minLength="10"
                :maxLength="30"
                :uppercase="true"
                counter
            />

            <MdFileInput
                :ref="setFieldRef('documentos')"
                v-model="form.documentos"
                label="Documentos"
                multiple
                chips
                counter
                counterString="archivos seleccionados"
                accept=".pdf,image/*"
                :required="true"
                :maxSizeMB="2"
            />

            <MdRichText
                :ref="setFieldRef('comentarios')"
                v-model="form.comentarios"
                label="Comentarios"
                :required="true"
                :minLength="10"
                :maxLength="300"
                toolbar="minimal"
                helper="Entre 10 y 300 caracteres"
                placeholder="Escribe tus comentarios..."
            />

            <MdPasswordInput
                :ref="setFieldRef('password')"
                v-model="form.password"
                label="Contraseña"
                :required="true"
                :min-length="5"
                security="strong"
            />

            <MdTimeInput
                :ref="setFieldRef('password')"
                v-model="form.hora_simple"
                label="Hora simple"
            />

            <MdTimeInput
                :ref="setFieldRef('password')"
                v-model="form.hora_requerida"
                label="Hora requerida"
                :required="true"
            />

            <MdTimeInput
                :ref="setFieldRef('password')"
                v-model="form.hora_con_segundos"
                label="Con segundos"
                :use-seconds="true"
            />

                <v-btn type="submit" color="primary" block>
                    Probar validaciones
                </v-btn>

            </form>
        </section>

        <pre>
            {{ data }}
        </pre>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue';
import MdTextarea from '@/Components/MaterialDesign/MdTextareaInput.vue';
import MdDateInput from '@/Components/MaterialDesign/MdDateInput.vue';
import MdDatePicker from '@/Components/MaterialDesign/MdDatePicker.vue';
import MdSelect from '@/Components/MaterialDesign/MdSelect.vue';
import MdSelectSearch from '@/Components/MaterialDesign/MdSelectSearch.vue';
import MdSwitch from '@/Components/MaterialDesign/MdSwitch.vue';
import MdCheckbox from '@/Components/MaterialDesign/MdCheckbox.vue';
import MdRadioGroup from '@/Components/MaterialDesign/MdRadioGroup.vue';
import MdFileInput from '@/Components/MaterialDesign/MdFileInput.vue';
import MdPasswordInput from '@/Components/MaterialDesign/MdPasswordInput.vue';
import MdTimeInput from '@/Components/MaterialDesign/MdTimeInput.vue';

import MdRichText from '@/Components/MaterialDesign/MdRichTextArea.vue';

import { useMdFormValidation } from '@/utils/FormValidation';
import { reactive, ref, onMounted } from 'vue';

const form = reactive({
    nombre: '',
    correo: '',
    curp: '',
    edad: null,
    monto: null,
    saldo: null,
    comentarios: '',
    fecha_nacimiento: '',
    fecha_cita: [],
    fecha_rango: [],
    fecha_simple: '',
    frutas_id: null,
    activo: false,
    acepta_terminos: false,
    genero: '',
    documentos: [],
    contenido_texto: '',
    hora_simple: '',
    hora_requerida: '',
    hora_con_segundos: '',
});

const Frutas = ref([
    { id: 1, nombre: 'Manzana' },
    { id: 2, nombre: 'Plátano' },
    { id: 3, nombre: 'Naranja' },
    { id: 4, nombre: 'Mango' },
    { id: 5, nombre: 'Fresa' },
    { id: 6, nombre: 'Uva' },
    { id: 7, nombre: 'Sandía' }
]);

const Colores = ref([
    { id: 1, nombre: 'Rojo' },
    { id: 2, nombre: 'Azul' },
    { id: 3, nombre: 'Verde' },
    { id: 4, nombre: 'Amarillo' },
    { id: 5, nombre: 'Negro' },
    { id: 6, nombre: 'Blanco' },
    { id: 7, nombre: 'Gris' },
])

const data = ref({});
const { setFieldRef, validateAll } = useMdFormValidation();

const handleSubmit = () => {
    const allValid = validateAll();

    if (!allValid) {
        console.log("Formulario inválido");
        return;
    }

    data.value = { ...form };
    console.log("Formulario válido:", data.value);
};


onMounted(() => {
});
</script>

<style scoped>
.quill-comentarios .ql-toolbar.ql-snow {
    border: 1px solid #d1d5db;
    border-bottom: none;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    padding: 4px 8px;
}

.quill-comentarios .ql-container.ql-snow {
    border: 1px solid #d1d5db;
    border-top: none;
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.quill-comentarios .ql-editor {
    min-height: 120px;
    max-height: 220px;
    overflow-y: auto;
    overflow-wrap: break-word;
    word-break: break-word;
    padding: 8px 12px;
    font-size: 0.95rem;
}
</style>


