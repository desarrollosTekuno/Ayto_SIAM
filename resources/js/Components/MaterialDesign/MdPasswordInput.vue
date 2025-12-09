<!-- resources/js/Components/MaterialDesign/MdPasswordInput.vue -->
<template>
    <div class="w-full">
        <v-text-field
            ref="inputRef"
            v-model="innerValue"
            :label="label"
            :id="id"
            :name="name"
            :type="inputType"
            :append-inner-icon="appendIcon"
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
            autocomplete="new-password"
            @click:append-inner="toggleVisibility"
            @keydown="handleKeydown"
            @blur="handleBlur"
        />
    </div>
</template>

<script setup lang="ts">
import {
    computed,
    ref,
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

type ModelValue = string | null | undefined;

// Niveles de seguridad (solo estructura)
type PasswordSecurity = 'simple' | 'basic' | 'strong' | 'strict';

interface MdPasswordInputProps {
    modelValue?: ModelValue;
    label?: string;
    icon?: string;
    id?: string;
    name?: string;
    required?: boolean;
    variant?: Variant;
    clearable?: boolean;
    minLength?: number | null;
    maxLength?: number | null;
    counter?: boolean;
    helper?: string;
    readonly?: boolean;
    externalError?: string;
    pattern?: string | RegExp | null;
    showSuccessState?: boolean;
    density?: Density;
    rounded?: boolean | string | number;
    security?: PasswordSecurity | null;
    /**
     * Nivel de seguridad de la contraseña (solo estructura):
     * - 'simple' : al menos 1 letra
     * - 'basic'  : al menos 1 letra y 1 número
     * - 'strong' : al menos 1 mayúscula, 1 minúscula y 1 número
     * - 'strict' : al menos 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial
     *
     * Si es null, NO se valida estructura adicional.
     */

}

const props = withDefaults(defineProps<MdPasswordInputProps>(), {
    modelValue: '',
    label: '',
    id: undefined,
    name: undefined,
    required: false,
    variant: 'outlined',
    clearable: false,
    minLength: null,
    maxLength: null,
    counter: false,
    helper: '',
    readonly: false,
    externalError: '',
    pattern: null,
    showSuccessState: true,
    density: 'compact',
    rounded: 'sm',
    security: null,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const errorMessage = ref<string>('');
const touched = ref(false);
const showPassword = ref(false);

// ref al v-text-field para poder hacer focus()
const inputRef = ref<any | null>(null);

// MdFormContext
const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdPasswordInput_${props.id || instance?.uid || Math.random().toString(36)}`;

const inputType = computed(() => (showPassword.value ? 'text' : 'password'));
const appendIcon = computed(() =>
    showPassword.value ? 'mdi-eye-off' : 'mdi-eye'
);

// v-model interno
const innerValue = computed<ModelValue>({
    get() {
        return props.modelValue;
    },
    set(value) {
        const type: AllowedType = 'password';
        let newValue: ModelValue = value;

        newValue = sanitizeByType(
            newValue,
            type,
            false, // no upper en contraseñas
            props.maxLength ?? undefined
        );

        emit('update:modelValue', newValue);
    },
});

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

/**
 * Valida estructura de la contraseña según el nivel de seguridad.
 * Devuelve mensaje de error o null si pasa.
 */
const validateSecurityStructure = (
    value: string,
    security: PasswordSecurity
): string | null => {
    const hasLetter = /[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]/.test(value);
    const hasNumber = /[0-9]/.test(value);
    const hasUpper = /[A-ZÁÉÍÓÚÜÑ]/.test(value);
    const hasLower = /[a-záéíóúüñ]/.test(value);
    const hasSpecial = /[!@#$%^&*()_\+\-\.?]/.test(value);

    switch (security) {
        case 'simple':
            if (!hasLetter) {
                return 'Debe contener al menos una letra.';
            }
            break;

        case 'basic':
            if (!hasLetter || !hasNumber) {
                return 'Debe contener al menos una letra y un número.';
            }
            break;

        case 'strong':
            if (!hasUpper || !hasLower || !hasNumber) {
                return 'Debe contener mayúsculas, minúsculas y al menos un número.';
            }
            break;

        case 'strict':
            if (!hasUpper || !hasLower || !hasNumber || !hasSpecial) {
                return 'Debe contener mayúsculas, minúsculas, un número y un carácter especial.';
            }
            break;
    }

    return null;
};

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    const raw = props.modelValue ?? '';
    const value = String(raw);
    const trimmed = value.trim();
    const length = trimmed.length;

    // required
    if (props.required && !length) {
        errorMessage.value = 'Este campo es obligatorio';
        return false;
    }

    // vacío y no requerido: no validar nada más
    if (!length && !props.required) {
        errorMessage.value = '';
        return true;
    }

    // longitud
    if (props.minLength != null && length < props.minLength) {
        errorMessage.value = `Debe tener al menos ${props.minLength} caracteres`;
        return false;
    }

    if (props.maxLength != null && length > props.maxLength) {
        errorMessage.value = `Debe tener máximo ${props.maxLength} caracteres`;
        return false;
    }

    // pattern opcional
    if (props.pattern) {
        let regex: RegExp | null = null;

        if (props.pattern instanceof RegExp) {
            regex = props.pattern;
        } else if (typeof props.pattern === 'string') {
            try {
                regex = new RegExp(props.pattern);
            } catch {
                regex = null;
            }
        }

        if (regex && !regex.test(value)) {
            errorMessage.value = 'El formato no es válido.';
            return false;
        }
    }

    // seguridad (solo estructura) si se especifica
    if (props.security) {
        const secError = validateSecurityStructure(value, props.security);
        if (secError) {
            errorMessage.value = secError;
            return false;
        }
    }

    errorMessage.value = '';
    return true;
};

const handleBlur = () => {
    validate();
};

const handleKeydown = (e: KeyboardEvent) => {
    const type: AllowedType = 'password';
    if (!isValidKey(e, type)) {
        e.preventDefault();
    }
};

const toggleVisibility = () => {
    if (props.readonly) return;
    showPassword.value = !showPassword.value;
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

.md-input-success :deep(.v-field__append-inner .v-icon) {
    color: #16a34a !important;
}
</style>
