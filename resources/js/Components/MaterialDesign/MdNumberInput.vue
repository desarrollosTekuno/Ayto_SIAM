<!-- resources/js/Components/MaterialDesign/MdNumberInput.vue -->
<template>
    <div class="w-full">
        <v-text-field
            ref="inputRef"
            v-model="innerValue"
            :label="label"
            :id="id"
            :name="name"
            type="text"
            :prepend-inner-icon="icon"
            :density="density"
            :variant="variant"
            :rounded="rounded"
            :error="!!displayedError"
            :error-messages="displayedError"
            :clearable="clearable"
            :maxlength="maxLength ?? undefined"
            :counter="counter && !!maxLength"
            :class="successClass"
            :hint="helper"
            :persistent-hint="!!helper"
            :readonly="readonly"
            autocomplete="off"
            @keydown="handleKeydown"
            @input="handleInput"
            @blur="handleBlur"
        />
    </div>
</template>

<script setup lang="ts">
import {
    computed,
    ref,
    watch,
    onMounted,
    onBeforeUnmount,
    getCurrentInstance,
} from 'vue';
import {
    isValidKey,
    sanitizeByType,
    type AllowedType,
} from '@/utils/FieldUtils';
import { useMdForm } from '@/utils/MdFormContext';

type Density = 'default' | 'comfortable' | 'compact';
type Variant =
    | 'outlined'
    | 'filled'
    | 'plain'
    | 'solo'
    | 'solo-filled'
    | 'solo-inverted'
    | 'underlined';

type NumericAllowed = Extract<
    AllowedType,
    'number' | 'decimal' | 'negative' | 'money'
>;
type ModelValue = number | null | undefined;

interface MdNumberInputProps {
    modelValue?: ModelValue;
    id?: string;
    name?: string;
    label?: string;
    icon?: string;
    required?: boolean;
    allowed?: NumericAllowed;
    min?: number | null;
    max?: number | null;
    maxLength?: number | null;
    variant?: Variant;
    clearable?: boolean;
    counter?: boolean;
    helper?: string;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    density?: Density;
    rounded?: boolean | string | number;
    allowNegative?: boolean;
}

const props = withDefaults(defineProps<MdNumberInputProps>(), {
    modelValue: null,
    id: undefined,
    name: undefined,
    label: '',
    icon: '',
    required: false,
    allowed: 'decimal',
    min: null,
    max: null,
    maxLength: null,
    variant: 'outlined',
    clearable: false,
    counter: false,
    helper: '',
    readonly: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    rounded: 'sm',
    allowNegative: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const rawValue = ref<string>('');
const errorMessage = ref<string>('');
const touched = ref(false);

const inputRef = ref<any | null>(null);


const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdNumberInput_${props.id || instance?.uid || Math.random().toString(36)}`;


const numericType = computed<AllowedType>(() => {
    const base = props.allowed ?? 'decimal';

    if (props.allowNegative) {
        // Enteros negativos
        if (base === 'number') return 'negativeInt';
        // Decimales / money negativos
        return 'negative';
    }

    // Solo positivos
    if (base === 'money') return 'decimal';
    if (base === 'negative') return 'decimal';
    return base;
});

// v-model interno del <v-text-field>
const innerValue = computed<string>({
    get() {
        return rawValue.value;
    },
    set(value: string) {
        rawValue.value = value;

        const sanitized = sanitizeByType(
            value,
            numericType.value,
            false,
            props.maxLength ?? undefined
        );

        const trimmed = sanitized.trim();

        if (!trimmed) {
            emit('update:modelValue', null);
            errorMessage.value = '';
            rawValue.value = sanitized;
            return;
        }

        const num = Number(trimmed);
        if (Number.isNaN(num)) {
            emit('update:modelValue', null);
            return;
        }

        emit('update:modelValue', num);
        rawValue.value = sanitized;
    },
});

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    const text = (rawValue.value ?? '').trim();

    // Required
    if (props.required && !text.length) {
        errorMessage.value = 'Este campo es obligatorio';
        return false;
    }

    if (!props.required && !text.length) {
        errorMessage.value = '';
        return true;
    }

    const num = Number(text);

    if (Number.isNaN(num)) {
        errorMessage.value = 'El valor debe ser numérico';
        return false;
    }

    if (props.min != null && num < props.min) {
        errorMessage.value = `El valor mínimo permitido es ${props.min}`;
        return false;
    }

    if (props.max != null && num > props.max) {
        errorMessage.value = `El valor máximo permitido es ${props.max}`;
        return false;
    }

    errorMessage.value = '';
    return true;
};

const handleBlur = () => {
    validate();
};

const handleInput = () => {
    if (!touched.value) touched.value = true;

    if (props.required || props.min != null || props.max != null) {
        validate();
    }
};

const handleKeydown = (e: KeyboardEvent) => {
    if (!isValidKey(e, numericType.value)) {
        e.preventDefault();
    }
};

const focus = () => {
    const comp = inputRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLInputElement | null =
        comp.$el?.querySelector?.('input') ?? null;

    el?.focus();
};

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

// Sincronizar cuando el padre cambie el modelValue desde fuera
watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val == null ? '' : String(val);
    },
    { immediate: true }
);

// Registro automático en MdFormContext
onMounted(() => {
    if (mdForm) {
        mdForm.registerField(fieldKey, {
            validate,
            focus,
        });
    }
});

onBeforeUnmount(() => {
    if (mdForm) {
        mdForm.unregisterField(fieldKey);
    }
});

defineExpose({
    validate,
    focus,
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
