<!-- resources/js/Components/MaterialDesign/MdTextInput.vue -->
<template>
    <div class="w-full h-18 py-2 px-2">
        <v-text-field
            ref="inputRef"
            v-model="innerValue"
            :label="label"
            :type="type"
            :prepend-inner-icon="icon"
            :density="density"
            :variant="variant"
            :rounded="rounded"
            :error="!!displayedError"
            :error-messages="displayedError"
            :clearable="clearable && !readonly"
            :maxlength="maxLength ?? undefined"
            :counter="counter && !!maxLength"
            :class="[successClass, readonlyClass]"
            :hint="computedHint"
            :persistent-hint="persistentHint"
            :readonly="readonly"
            autocomplete="off"
            hide-details="auto"
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
    toUpper,
    isValidKey,
    sanitizeByType,
    type AllowedType,
} from '@/utils/FieldUtils';
// @ts-ignore
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
    modelValue?: ModelValue;
    label?: string;
    icon?: string;
    type?: string;
    required?: boolean;
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
    name?: string;
}

const props = withDefaults(defineProps<MdTextInputProps>(), {
    modelValue: '',
    label: '',
    icon: '',
    type: 'text',
    required: false,
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
    name: undefined,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const errorMessage = ref<string>('');
const touched = ref(false);
const inputRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name || `MdTextInput_${instance?.uid ?? Math.random().toString(36)}`;

const innerValue = computed<ModelValue>({
    get() {
        return props.modelValue;
    },
    set(value) {
        let newValue: ModelValue = value;
        const type: AllowedType = props.allowed ?? 'any';

        if (type === 'any') {
            if (typeof newValue === 'string') {
                let s = newValue;
                s = toUpper(s, props.uppercase ?? true);

                if (typeof props.maxLength === 'number' && props.maxLength > 0) {
                    s = s.slice(0, props.maxLength);
                }

                newValue = s;
            }
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

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

/** Hint: si es readonly, lo dejamos explícito */
const computedHint = computed<string>(() => {
    if (displayedError.value) return '';
    if (props.readonly) return 'Solo lectura';
    return (props.helper ?? '').trim();
});

const persistentHint = computed<boolean>(() => {
    return !!computedHint.value && (!!props.helperPersistent || props.readonly);
});

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    const raw = props.modelValue ?? '';
    const value = String(raw).trim();
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

const handleBlur = () => {
    validate();
};

const handleKeydown = (e: KeyboardEvent) => {
    if (props.readonly) {
        e.preventDefault();
        return;
    }

    const type: AllowedType = props.allowed ?? 'any';
    if (!isValidKey(e, type)) {
        e.preventDefault();
    }
};

const focus = () => {
    if (props.readonly) return;

    const comp = inputRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLInputElement | null =
        (comp.$el && comp.$el.querySelector && comp.$el.querySelector('input')) ||
        null;

    el?.focus();
};

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

const readonlyClass = computed<string>(() => {
    return props.readonly ? 'md-input-readonly' : '';
});

onMounted(() => {
    if (mdForm) {
        mdForm.registerField(fieldKey, { validate, focus });
    }
});

onBeforeUnmount(() => {
    if (mdForm) {
        mdForm.unregisterField(fieldKey);
    }
});

defineExpose({ validate, focus });
</script>

<style scoped>
/* SUCCESS */
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

/* READONLY */
.md-input-readonly :deep(.v-field) {
    background: rgba(148, 163, 184, 0.18) !important;
}

.md-input-readonly :deep(.v-field__outline__start),
.md-input-readonly :deep(.v-field__outline__notch),
.md-input-readonly :deep(.v-field__outline__end) {
    border-style: dashed !important;
    border-color: rgba(100, 116, 139, 0.75) !important;
}

.md-input-readonly :deep(input) {
    cursor: not-allowed !important;
    color: rgba(15, 23, 42, 0.75) !important;
}

.md-input-readonly :deep(.v-label) {
    color: rgba(100, 116, 139, 0.9) !important;
}

.md-input-readonly :deep(.v-field__prepend-inner .v-icon) {
    color: rgba(100, 116, 139, 0.9) !important;
}
</style>
