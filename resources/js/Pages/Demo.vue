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
    frutas_id: null
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
