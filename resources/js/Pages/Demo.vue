<template>
    <AppLayout>
        <template #header>
            <div class="w-full text-center">
                <span class="my-2 text-xl font-extrabold text-uppercase text-emerald-600">Ejemplos de componentes del sistema con vuetify</span>
            </div>
        </template>

          <Stepper
    v-model="step"
    :items="steps"
    mobile
    @finish="onFinish"
  >
    <template #item="{ step }">
      <div class="pa-4">
        <h3 class="mb-2 text-h6">{{ step.title }}</h3>

        <div v-if="step.value === 1">
          Contenido del paso 1
        </div>
        <div v-else-if="step.value === 2">
          Contenido del paso 2
        </div>
        <div v-else-if="step.value === 3">
          Resumen / Confirmación
        </div>
      </div>
    </template>

    <template #actions="{ prev, next, isLast }">
      <v-spacer />
      <v-btn variant="text" @click="prev">
        Atrás
      </v-btn>
      <v-btn color="primary" @click="next">
        {{ isLast ? 'Guardar' : 'Siguiente' }}
      </v-btn>
    </template>
  </Stepper>

        <div class="mx-2">
            <!-- Tabs con scroll -->
            <div class="overflow-x-auto">
                <div class="px-4 min-w-max">
                    <v-tabs
                        v-model="activeTab"
                        density="comfortable"
                        class="mb-2"
                        show-arrows
                    >
                        <v-tab
                            v-for="t in tabs"
                            :key="t.value"
                            :value="t.value"
                        >
                            {{ t.label }}
                        </v-tab>
                    </v-tabs>
                </div>
            </div>

            <!-- Contenido de tabs -->
            <v-window v-model="activeTab" class="pt-4">
                <!-- TEXT INPUT -->
                <v-window-item value="text">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleTextSubmit"
                            @invalid="notifyInvalid('text')"
                        >
                            <div class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <MdTextInput
                                    v-model="textForm.hechizo"
                                    label="Nombre del hechizo"
                                    icon="mdi-auto-fix"
                                    :required="true"
                                    :minLength="3"
                                    :maxLength="40"
                                    helper="Ej: BOLA DE FUEGO ETERNA"
                                />

                                <MdTextInput
                                    v-model="textForm.ingrediente"
                                    label="Ingrediente principal"
                                    icon="mdi-flask-outline"
                                    :uppercase="false"
                                    :required="true"
                                    helper="Ej: lágrima de salamandra"
                                />

                                <MdTextInput
                                    v-model="textForm.codigo"
                                    label="Código rúnico"
                                    icon="mdi-code-braces"
                                    :required="true"
                                    allowed="alphanumeric"
                                    :pattern="/^[A-Z0-9]{6}$/"
                                    helper="6 caracteres exactos"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Probar TextInput
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- EMAIL -->
                <v-window-item value="email">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleEmailSubmit"
                            @invalid="notifyInvalid('email')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdEmailInput
                                    v-model="emailForm.correoMago"
                                    label="Correo del mago"
                                    helper="Ej: merlin@torrearcana.com"
                                    :required="true"
                                />

                                <MdEmailInput
                                    v-model="emailForm.respaldo"
                                    label="Correo de respaldo"
                                    :required="true"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Enviar búho electrónico
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- PHONE -->
                <v-window-item value="phone">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handlePhoneSubmit"
                            @invalid="notifyInvalid('phone')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdPhoneInput
                                    v-model="phoneForm.torre"
                                    label="Teléfono de la torre"
                                    :required="true"
                                    helper="Ej: 2221234567"
                                />

                                <MdPhoneInput
                                    v-model="phoneForm.dragon"
                                    label="Teléfono del dragón"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Guardar números mágicos
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- NUMBER -->
                <v-window-item value="number">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleNumberSubmit"
                            @invalid="notifyInvalid('number')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdNumberInput
                                    v-model="numberForm.nivel"
                                    label="Nivel del hechizo"
                                    icon="mdi-star-circle-outline"
                                    :allowed="'number'"
                                    :min="1"
                                    :max="9"
                                    :required="true"
                                    helper="Del 1 al 9"
                                />

                                <MdNumberInput
                                    v-model="numberForm.mana"
                                    label="Costo de maná"
                                    icon="mdi-water-opacity"
                                    :allowed="'decimal'"
                                    :required="true"
                                />

                                <MdNumberInput
                                    v-model="numberForm.deudaHadas"
                                    label="Deuda con hadas"
                                    icon="mdi-currency-usd"
                                    :allowed="'money'"
                                    :allowNegative="true"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Calcular energía arcana
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- PASSWORD -->
                <v-window-item value="password">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handlePasswordSubmit"
                            @invalid="notifyInvalid('password')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdPasswordInput
                                    v-model="passwordForm.basica"
                                    label="Clave de la torre"
                                    :required="true"
                                    :minLength="5"
                                    helper="Algo que no adivine el goblin de sistemas"
                                />

                                <MdPasswordInput
                                    v-model="passwordForm.fuerte"
                                    label="Sello maestro"
                                    :required="true"
                                    :minLength="8"
                                    security="strong"
                                    helper="Mayúsculas, minúsculas y números"
                                />

                                <MdPasswordInput
                                    v-model="passwordForm.ultra"
                                    label="Contraseña del grimorio prohibido"
                                    :required="true"
                                    :minLength="10"
                                    security="strict"
                                    helper="Nivel paranoico recomendado"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Guardar contraseñas arcanas
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- DATE INPUT -->
                <v-window-item value="date-input">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleDateInputSubmit"
                            @invalid="notifyInvalid('date-input')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdDateInput
                                    v-model="dateInputForm.ritual"
                                    label="Fecha del ritual"
                                    :required="true"
                                />

                                <MdDateInput
                                    v-model="dateInputForm.portales"
                                    label="Ventana de portales"
                                    :range="true"
                                    :required="true"
                                />

                                <MdDateInput
                                    v-model="dateInputForm.prohibidos"
                                    label="Días prohibidos"
                                    :multiple="true"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Guardar calendario mágico
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- DATE PICKER -->
                <v-window-item value="date-picker">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleDatePickerSubmit"
                            @invalid="notifyInvalid('date-picker')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdDatePicker
                                    v-model="datePickerForm.simple"
                                    label="Día de invocación"
                                    :required="true"
                                />

                                <MdDatePicker
                                    v-model="datePickerForm.rango"
                                    label="Rango lunar"
                                    :range="true"
                                    :required="true"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Registrar fechas lunares
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- SELECTS -->
                <v-window-item value="select">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleSelectSubmit"
                            @invalid="notifyInvalid('select')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdSelect
                                    v-model="selectForm.bestia"
                                    label="Bestia favorita"
                                    :items="bestias"
                                    item-title="nombre"
                                    item-value="id"
                                    :required="true"
                                />

                                <MdSelectSearch
                                    v-model="selectForm.elementos"
                                    label="Elementos dominados"
                                    :items="elementos"
                                    item-title="nombre"
                                    item-value="id"
                                    multiple
                                    chips
                                    :minSelected="1"
                                    :maxSelected="3"
                                    helper="Máximo 3, no eres el avatar"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Guardar afinidades
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- BOOLEANOS -->
                <v-window-item value="boolean">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleBooleanSubmit"
                            @invalid="notifyInvalid('boolean')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdCheckbox
                                    v-model="booleanForm.aceptaRiesgos"
                                    label="Acepto riesgos mágicos extremos"
                                    :required="true"
                                    color="orange-darken-3"
                                />

                                <MdSwitch
                                    v-model="booleanForm.silencioso"
                                    label="Modo silencioso"
                                    color="indigo-darken-3"
                                />

                                <MdToggle
                                    v-model="booleanForm.turno"
                                    label="¿Turno nocturno?"
                                    left-label="No"
                                    right-label="Sí"
                                    :required="true"
                                />

                                <MdRadioGroup
                                    v-model="booleanForm.rango"
                                    label="Rango del mago"
                                    :items="[
                                        { value: 'aprendiz', label: 'Aprendiz' },
                                        { value: 'archimago', label: 'Archimago' },
                                        { value: 'jubilado', label: 'Jubilado feliz' },
                                    ]"
                                    :required="true"
                                    color="orange-darken-3"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Registrar nivel mágico
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- TEXTAREA -->
                <v-window-item value="textarea">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleTextareaSubmit"
                            @invalid="notifyInvalid('textarea')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdTextarea
                                    v-model="textareaForm.diario"
                                    label="Diario del mago"
                                    :required="true"
                                    :minLength="10"
                                    :maxLength="120"
                                    helper="Describe tu día mágico"
                                    counter
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Guardar anotación
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- FILE INPUT -->
                <v-window-item value="file">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleFileSubmit"
                            @invalid="notifyInvalid('file')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdFileInput
                                    v-model="fileForm.documentos"
                                    label="Pergaminos prohibidos"
                                    multiple
                                    chips
                                    counter
                                    accept=".pdf,image/*"
                                    :required="true"
                                    :max-size-m-b="2"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Cargar documentos arcanos
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- RICH TEXT -->
                <v-window-item value="richtext">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleRichSubmit"
                            @invalid="notifyInvalid('richtext')"
                        >
                            <div class="space-y-4">
                                <MdRichText
                                    v-model="richForm.grimorio"
                                    label="Entrada del grimorio"
                                    :required="true"
                                    :minLength="10"
                                    :maxLength="300"
                                    toolbar="essential"
                                    helper="Describe la receta o conjuro"
                                    placeholder="Escribe tu sabiduría arcana..."
                                />

                                <v-btn color="primary" type="submit">
                                    Guardar capítulo del grimorio
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- TIME INPUT -->
                <v-window-item value="time">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleTimeSubmit"
                            @invalid="notifyInvalid('time')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdTimeInput
                                    v-model="timeForm.simple"
                                    label="Hora del ritual"
                                    :required="true"
                                />

                                <MdTimeInput
                                    v-model="timeForm.conSegundos"
                                    label="Invocación precisa"
                                    :required="true"
                                    :use-seconds="true"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Registrar horario
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- TOGGLE -->
                <v-window-item value="toggle">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleToggleSubmit"
                            @invalid="notifyInvalid('toggle')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdToggle
                                    v-model="toggleForm.luz"
                                    label="Encender luz mágica"
                                    left-label="No"
                                    right-label="Sí"
                                    :required="true"
                                />

                                <MdToggle
                                    v-model="toggleForm.canal"
                                    label="Canal de hechizo"
                                    left-label="Etéreo"
                                    right-label="Físico"
                                    :left-value="'etereo'"
                                    :right-value="'fisico'"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Activar controles
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- SLIDER -->
                <v-window-item value="slider">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleSliderSubmit"
                            @invalid="notifyInvalid('slider')"
                        >
                            <div
                                class="grid items-start grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
                            >
                                <MdSlider
                                    v-model="sliderForm.volumen"
                                    label="Poder del encantamiento"
                                    :min="0"
                                    :max="100"
                                    thumb-label="always"
                                    :required="true"
                                    color="secondary"
                                    :show-ticks="true"
                                />

                                <v-btn
                                    color="primary"
                                    type="submit"
                                    class="mt-4 md:col-span-2 xl:col-span-3"
                                >
                                    Ajustar poder mágico
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>

                <!-- UPLOAD AREA -->
                <v-window-item value="upload">
                    <div class="p-4 bg-white border shadow-sm rounded-2xl md:p-6">
                        <FormValidate
                            @submit="handleUploadSubmit"
                            @invalid="notifyInvalid('upload')"
                        >
                            <div class="space-y-4">
                                <MdUploadArea
                                    v-model="uploadForm.archivos"
                                    label="Documentos arcanos"
                                    description="Sube pergaminos, sellos y contratos mágicos."
                                    multiple
                                    accept=".pdf,image/*"
                                    :required="true"
                                    :max-size-m-b="5"
                                    :max-files="5"
                                />

                                <v-btn color="primary" type="submit">
                                    Enviar artefactos
                                </v-btn>
                            </div>
                        </FormValidate>
                    </div>
                </v-window-item>
            </v-window>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Stepper from '@/Components/Stepper.vue'
import {
    MdTextInput,
    MdNumberInput,
    MdTextarea,
    MdDateInput,
    MdDatePicker,
    MdSelect,
    MdSelectSearch,
    MdSwitch,
    MdCheckbox,
    MdRadioGroup,
    MdFileInput,
    MdPasswordInput,
    MdTimeInput,
    MdToggle,
    MdSlider,
    MdUploadArea,
    MdEmailInput,
    MdPhoneInput,
    MdRichText,
} from '@/Components/MaterialDesign';


const step = ref(1)

const steps = [
    { value: 1, title: 'Datos personales', subtitle: 'Nombre, correo…' },
    { value: 2, title: 'Datos laborales', subtitle: 'Puesto, depto…' },
    { value: 3, title: 'Confirmación' },
]

const activeTab = ref('text');

const tabs = [
    { value: 'text', label: 'TextInput' },
    { value: 'email', label: 'Email' },
    { value: 'phone', label: 'Phone' },
    { value: 'number', label: 'Number' },
    { value: 'password', label: 'Password' },
    { value: 'date-input', label: 'Date Input' },
    { value: 'date-picker', label: 'Date Picker' },
    { value: 'select', label: 'Selects' },
    { value: 'boolean', label: 'Checkbox / Switch / Toggle' },
    { value: 'textarea', label: 'Textarea' },
    { value: 'file', label: 'File Input' },
    { value: 'richtext', label: 'Rich Text' },
    { value: 'time', label: 'Time Input' },
    { value: 'toggle', label: 'Toggle' },
    { value: 'slider', label: 'Slider' },
    { value: 'upload', label: 'Upload Area' },
];

const bestias = [
    { id: 1, nombre: 'Dragón carmesí' },
    { id: 2, nombre: 'Fénix azul' },
    { id: 3, nombre: 'Lobo espectral' },
];

const elementos = [
    { id: 1, nombre: 'Fuego' },
    { id: 2, nombre: 'Hielo' },
    { id: 3, nombre: 'Sombras' },
    { id: 4, nombre: 'Luz' },
];

const textForm = reactive({ hechizo: '', ingrediente: '', codigo: '' });
const emailForm = reactive({ correoMago: '', respaldo: '' });
const phoneForm = reactive({ torre: '', dragon: '' });
const numberForm = reactive({ nivel: null, mana: null, deudaHadas: null });
const passwordForm = reactive({ basica: '', fuerte: '', ultra: '' });
const dateInputForm = reactive({ ritual: null, portales: [], prohibidos: [] });
const datePickerForm = reactive({ simple: null, rango: [] });
const selectForm = reactive({ bestia: null, elementos: [] });

const booleanForm = reactive({
    aceptaRiesgos: false,
    silencioso: false,
    turno: null,
    rango: null,
});

const textareaForm = reactive({ diario: '' });
const fileForm = reactive({ documentos: null });
const richForm = reactive({ grimorio: '' });
const timeForm = reactive({ simple: null, conSegundos: null });
const toggleForm = reactive({ luz: null, canal: null });
const sliderForm = reactive({ volumen: null });
const uploadForm = reactive({ archivos: null });

const onFinish = () => {
    console.log('Finalizó el stepper')
}

const handleTextSubmit = async () => {
    if (!textFormRef.value.validateAll())
    return notifyInvalid("TextInput")();
    await simulatePost('/demo/textinput', textForm);
};

const handleEmailSubmit = async () => {
    if (!emailFormRef.value.validateAll()) return notifyInvalid("Email")();

    await simulatePost('/demo/email', emailForm);
};

const handlePhoneSubmit = async () => {
    if (!phoneFormRef.value.validateAll()) return notifyInvalid("Phone")();

    await simulatePost('/demo/phone', phoneForm);
};

const handleNumberSubmit = async () => {
    if (!numberFormRef.value.validateAll()) return notifyInvalid("Number")();

    await simulatePost('/demo/number', numberForm);
};

const handlePasswordSubmit = async () => {
    if (!passwordFormRef.value.validateAll()) return notifyInvalid("Password")();

    await simulatePost('/demo/password', passwordForm);
};

const handleDateInputSubmit = async () => {
    if (!dateInputFormRef.value.validateAll()) return notifyInvalid("Date Input")();

    await simulatePost('/demo/date-input', dateInputForm);
};

const handleDatePickerSubmit = async () => {
    if (!datePickerFormRef.value.validateAll()) return notifyInvalid("Date Picker")();

    await simulatePost('/demo/date-picker', datePickerForm);
};

const handleSelectSubmit = async () => {
    if (!selectFormRef.value.validateAll()) return notifyInvalid("Selects")();

    await simulatePost('/demo/select', selectForm);
};

const handleBooleanSubmit = async () => {
    if (!booleanFormRef.value.validateAll()) return notifyInvalid("Boolean")();

    await simulatePost('/demo/boolean', booleanForm);
};

const handleTextareaSubmit = async () => {
    if (!textareaFormRef.value.validateAll()) return notifyInvalid("Textarea")();

    await simulatePost('/demo/textarea', textareaForm);
};

const handleFileSubmit = async () => {
    if (!fileFormRef.value.validateAll()) return notifyInvalid("File Input")();

    await simulatePost('/demo/file', fileForm);
};

const handleRichSubmit = async () => {
    if (!richFormRef.value.validateAll()) return notifyInvalid("Rich Text")();

    await simulatePost('/demo/richtext', richForm);
};

const handleTimeSubmit = async () => {
    if (!timeFormRef.value.validateAll()) return notifyInvalid("Time Input")();

    await simulatePost('/demo/time', timeForm);
};

const handleToggleSubmit = async () => {
    if (!toggleFormRef.value.validateAll()) return notifyInvalid("Toggle")();

    await simulatePost('/demo/toggle', toggleForm);
};

const handleSliderSubmit = async () => {
    if (!sliderFormRef.value.validateAll()) return notifyInvalid("Slider")();

    await simulatePost('/demo/slider', sliderForm);
};

const handleUploadSubmit = async () => {
    if (!uploadFormRef.value.validateAll()) return notifyInvalid("Upload Area")();

    await simulatePost('/demo/upload-area', uploadForm);
};


const notifyInvalid = section => () =>
    console.warn(`%cFormulario inválido en sección: ${section}`, 'color: red');
</script>
