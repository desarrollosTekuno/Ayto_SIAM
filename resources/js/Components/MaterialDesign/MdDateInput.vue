<!-- resources/js/Components/Inputs/MdDateInput.vue -->
<template>
    <div class="w-full max-w-xs">
        <v-date-input
            v-model="innerValue"
            :label="label"

            :multiple="computedMultiple"
            :min="min"
            :max="max"
            :locale="locale"

            :variant="variant"
            :density="density"
            :rounded="rounded"
            hide-details="auto"
            :clearable="clearable"

            :hint="helper"
            :persistent-hint="!!helper"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"

            :prepend-inner-icon="icon"
            prepend-icon=""

            @blur="handleBlur"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

type Density = 'default' | 'comfortable' | 'compact';
type Variant =
    | 'outlined'
    | 'filled'
    | 'plain'
    | 'solo'
    | 'solo-filled'
    | 'solo-inverted'
    | 'underlined';

// IMPORTANTES: permitimos también Date / Date[]
type SingleDate = string | Date | null;
type MultiDate = (string | Date)[];
type ModelValue = SingleDate | MultiDate;

interface MdDateInputProps {
    modelValue?: ModelValue;
    label?: string;
    icon?: string;
    required?: boolean;
    helper?: string;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    density?: Density;
    variant?: Variant;
    rounded?: boolean | string | number;
    clearable?: boolean;

    min?: string | null;
    max?: string | null;
    multiple?: boolean;
    range?: boolean;
    locale?: string;
}

const props = withDefaults(defineProps<MdDateInputProps>(), {
    modelValue: null,
    label: '',
    icon: 'mdi-calendar',
    required: false,
    helper: '',
    readonly: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    variant: 'outlined',
    rounded: 'xl',
    clearable: true,
    min: null,
    max: null,
    multiple: false,
    range: false,
    locale: 'es-MX',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const rawValue = ref<ModelValue>(
    props.modelValue ?? (props.range || props.multiple ? [] : null)
);
const errorMessage = ref<string>('');
const touched = ref(false);

// Sincroniza cuando el padre cambia modelValue
watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val ?? (props.range || props.multiple ? [] : null);
    },
    { immediate: true }
);

// v-model hacia v-date-input
const innerValue = computed<ModelValue>({
    get() {
        return rawValue.value;
    },
    set(value) {
        rawValue.value = value;
        emit('update:modelValue', value);
    },
});

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

// Aquí está la CLAVE para que el rango funcione como el original
const computedMultiple = computed(() => {
    if (props.range) return 'range'; // igual que multiple="range" del ejemplo original
    if (props.multiple) return true;
    return false;
});

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    const v = rawValue.value;

    // Campo requerido
    if (props.required) {
        if (props.range || props.multiple) {
            const arr = Array.isArray(v) ? v : [];
            if (props.range) {
                if (arr.length !== 2) {
                    errorMessage.value = 'Selecciona un rango';
                    return false;
                }
            } else {
                if (arr.length === 0) {
                    errorMessage.value = 'Este campo es obligatorio';
                    return false;
                }
            }
        } else {
            if (!v) {
                errorMessage.value = 'Este campo es obligatorio';
                return false;
            }
        }
    }

    errorMessage.value = '';
    return true;
};

const handleBlur = () => {
    validate();
};

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

defineExpose({
    validate,
});
</script>

<style scoped>
.md-input-success :deep(.v-field__outline__start),
.md-input-success :deep(.v-field__outline__notch),
.md-input-success :deep(.v-field__outline__end) {
    border-color: #16a34a !important;
}

.md-input-success :deep(.v-label) {
    color: #16a34a !important;
}

.md-input-success :deep(.v-field__prepend-inner .v-icon) {
    color: #16a34a !important;
}
</style>
