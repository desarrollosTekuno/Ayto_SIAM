<!-- resources/js/Components/MaterialDesign/MdPasswordInput.vue -->
<template>
    <div class="w-full px-2 py-2 h-18">
        <v-text-field
            ref="inputRef"
            v-model="innerValue"
            :id="id"
            :name="name"
            :type="inputType"
            :append-inner-icon="appendIcon"
            :prepend-inner-icon="prependIcon"
            :density="density"
            :variant="variant"
            :rounded="rounded"
            :error="!!displayedError"
            :error-messages="displayedError"
            :clearable="clearable"
            :maxlength="maxLength ?? undefined"
            :counter="counter && !!maxLength"
            :class="successClass"
            :hint="computedHint"
            :persistent-hint="persistentHint"
            :readonly="readonly"
            autocomplete="new-password"
            hide-details="auto"
            @click:append-inner="toggleVisibility"
            @keydown="handleKeydown"
            @blur="handleBlur"
        >
            <template #label>
                <span class="inline-flex items-center gap-1">
                    <span
                        v-if="required"
                        class="font-bold text-red-500"
                    >*</span>

                    <span>{{ label }}</span>

                    <v-tooltip v-if="tooltip" location="top">
                        <template #activator="{ props }">
                            <v-icon
                                v-bind="props"
                                size="16"
                                class="text-gray-400 cursor-help"
                            >
                                mdi-information-outline
                            </v-icon>
                        </template>
                        <span>{{ tooltip }}</span>
                    </v-tooltip>
                </span>
            </template>
        </v-text-field>
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

type PasswordSecurity = 'simple' | 'basic' | 'strong' | 'strict';

interface MdPasswordInputProps {
    modelValue?: ModelValue;
    label?: string;
    icon?: string;
    id?: string;
    name?: string;
    required?: boolean;
    tooltip?: string;
    variant?: Variant;
    clearable?: boolean;
    minLength?: number | null;
    maxLength?: number | null;
    counter?: boolean;
    helper?: string;
    helperPersistent?: boolean;
    readonly?: boolean;
    externalError?: string;
    pattern?: string | RegExp | null;
    showSuccessState?: boolean;
    density?: Density;
    rounded?: boolean | string | number;
    security?: PasswordSecurity | null;
    /**
     * Nivel de seguridad de la contraseña
     * - 'simple' : al menos 1 letra
     * - 'basic'  : al menos 1 letra y 1 número
     * - 'strong' : al menos 1 may, 1 minuscula y 1 numero
     * - 'strict' : al menos 1 mayus, 1 , minus, 1 numero y 1 carácter especial
     *
     */

}

const props = withDefaults(defineProps<MdPasswordInputProps>(), {
    modelValue: '',
    label: '',
    id: undefined,
    name: undefined,
    required: false,
    tooltip: '',
    variant: 'outlined',
    clearable: true,
    minLength: null,
    maxLength: null,
    counter: false,
    helper: '',
    helperPersistent: false,
    readonly: false,
    externalError: '',
    pattern: null,
    showSuccessState: true,
    density: 'compact',
    rounded: 'lg',
    security: null,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const errorMessage = ref<string>('');
const touched = ref(false);
const showPassword = ref(false);

const inputRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdPasswordInput_${props.id || instance?.uid || Math.random().toString(36)}`;


const inputType = computed(() => (showPassword.value ? 'text' : 'password'));
const prependIcon = computed(() => props.icon || 'mdi-lock-outline');
const appendIcon = computed(() =>
    showPassword.value ? 'mdi-eye-off' : 'mdi-eye'
);

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
            false,
            props.maxLength ?? undefined
        );

        emit('update:modelValue', newValue);
    },
});

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

/** Si hay error, no mostramos helper para no mezclar mensajes */
const computedHint = computed<string>(() => {
    if (displayedError.value) return '';
    return (props.helper ?? '').trim();
});

/** Controla si el helper es persistente o solo al focus */
const persistentHint = computed<boolean>(() => {
    return !!computedHint.value && !!props.helperPersistent;
});

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
            if (!hasLetter) return 'Debe contener al menos una letra.';
            break;
        case 'basic':
            if (!hasLetter || !hasNumber)
                return 'Debe contener al menos una letra y un número.';
            break;
        case 'strong':
            if (!hasUpper || !hasLower || !hasNumber)
                return 'Debe contener mayúsculas, minúsculas y al menos un número.';
            break;
        case 'strict':
            if (!hasUpper || !hasLower || !hasNumber || !hasSpecial)
                return 'Debe contener mayúsculas, minúsculas, un número y un carácter especial.';
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

    if (props.required && !length) {
        errorMessage.value = 'Este campo es obligatorio';
        return false;
    }

    if (!length && !props.required) {
        errorMessage.value = '';
        return true;
    }

    if (props.minLength != null && length < props.minLength) {
        errorMessage.value = `Debe tener al menos ${props.minLength} caracteres`;
        return false;
    }

    if (props.maxLength != null && length > props.maxLength) {
        errorMessage.value = `Debe tener máximo ${props.maxLength} caracteres`;
        return false;
    }

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

onMounted(() => {
    mdForm?.registerField(fieldKey, { validate, focus });
});

onBeforeUnmount(() => {
    mdForm?.unregisterField(fieldKey);
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
