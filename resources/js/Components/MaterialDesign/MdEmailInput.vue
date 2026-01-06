<!-- resources/js/Components/MaterialDesign/MdEmailInput.vue -->
<template>
    <div class="w-full h-18 py-2 px-2">
        <v-text-field
            ref="inputRef"
            :id="id"
            :name="name"
            v-model="innerValue"
            :type="type"
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
            :hint="computedHint"
            :persistent-hint="persistentHint"
            :readonly="readonly"
            :autocomplete="'email'"
            hide-details="auto"
            @keydown="handleKeydown"
            @blur="handleBlur"
        >
            <!-- Label custom -->
            <template #label>
                <span class="inline-flex items-center gap-1">
                    <span
                        v-if="required"
                        class="text-red-500 font-bold"
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
    toUpper,
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

type ModelValue = string | number | null | undefined;

interface MdTextInputProps {
    id?: string;
    name?: string;
    modelValue?: ModelValue;
    label?: string;
    icon?: string;
    type?: string;
    required?: boolean;
    tooltip?: string;
    variant?: Variant;
    clearable?: boolean;
    uppercase?: boolean;
    minLength?: number | null;
    maxLength?: number | null;
    counter?: boolean;
    helper?: string;
    helperPersistent?: boolean;
    readonly?: boolean;
    externalError?: string;
    allowed?: AllowedType;
    pattern?: string | RegExp | null;
    showSuccessState?: boolean;
    density?: Density;
    rounded?: boolean | string | number;
}

const props = withDefaults(defineProps<MdTextInputProps>(), {
    id: undefined,
    name: undefined,
    modelValue: '',
    label: '',
    icon: '',
    type: 'email',
    required: false,
    tooltip: '',
    variant: 'outlined',
    clearable: true,
    uppercase: true,
    minLength: null,
    maxLength: null,
    counter: false,
    helper: '',
    helperPersistent: false,
    readonly: false,
    externalError: '',
    allowed: 'any',
    pattern: null,
    showSuccessState: true,
    density: 'compact',
    rounded: 'lg',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const errorMessage = ref('');
const touched = ref(false);
const inputRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name || `MdTextInput_${instance?.uid ?? Math.random().toString(36)}`;

const innerValue = computed<ModelValue>({
    get: () => props.modelValue,
    set(value) {
        let newValue: ModelValue = value;
        const type: AllowedType = props.allowed ?? 'any';

        if (type === 'any' && typeof newValue === 'string') {
            let s = toUpper(newValue, props.uppercase ?? true);

            if (typeof props.maxLength === 'number' && props.maxLength > 0) {
                s = s.slice(0, props.maxLength);
            }

            newValue = s;
        } else {
            newValue = sanitizeByType(
                newValue,
                type,
                props.uppercase ?? true,
                props.maxLength ?? undefined
            );
        }

        emit('update:modelValue', newValue);
    },
});

const displayedError = computed(() => {
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

const EMAIL_REGEX =/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;
    const value = String(props.modelValue ?? '').trim();
    const length = value.length;

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

    if (length) {
        const shouldValidateEmail = (props.type ?? 'email') === 'email';

        if (shouldValidateEmail && !props.pattern) {
            if (!EMAIL_REGEX.test(value)) {
                errorMessage.value = 'El correo no tiene un formato válido.';
                return false;
            }
        }
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

    errorMessage.value = '';
    return true;
};

const handleBlur = () => validate();

const handleKeydown = (e: KeyboardEvent) => {
    if (!isValidKey(e, props.allowed ?? 'any')) {
        e.preventDefault();
    }
};

const focus = () => {
    inputRef.value?.focus?.();
};

const successClass = computed(() => {
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

defineExpose({ validate, focus });
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
