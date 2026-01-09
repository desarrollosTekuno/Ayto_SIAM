<!-- resources/js/Components/MaterialDesign/MdNumberInput.vue -->
<template>
    <div class="w-full h-18 py-2 px-2">
        <v-text-field
            ref="inputRef"
            v-model="innerValue"
            :id="id"
            :name="name"
            type="text"
            :inputmode="inputMode"
            :pattern="inputPattern"
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
            @beforeinput="handleBeforeInput"
            @keydown="handleKeydown"
            @input="handleInput"
            @paste.prevent="handlePaste"
            @drop.prevent="handleDrop"
            @blur="handleBlur"
        >
            <template #label>
                <span class="inline-flex items-center gap-1">
                    <span v-if="required" class="text-red-500 font-bold">*</span>

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
    tooltip?: string;
    allowed?: NumericAllowed;
    min?: number | null;
    max?: number | null;
    maxLength?: number | null;
    variant?: Variant;
    clearable?: boolean;
    counter?: boolean;
    helper?: string;
    helperPersistent?: boolean;
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
    tooltip: '',
    allowed: 'number',
    min: null,
    max: null,
    maxLength: null,
    variant: 'outlined',
    clearable: true,
    counter: false,
    helper: '',
    helperPersistent: false,
    readonly: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    rounded: 'lg',
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
        if (base === 'number') return 'negativeInt';
        return 'negative';
    }

    if (base === 'money') return 'decimal';
    if (base === 'negative') return 'decimal';
    return base;
});

const sanitizeText = (text: string) =>
    sanitizeByType(
        text ?? '',
        numericType.value,
        false,
        props.maxLength ?? undefined
    );

const inputMode = computed(() => {
    return numericType.value === 'number' || numericType.value === 'negativeInt'
        ? 'numeric'
        : 'decimal';
});

const inputPattern = computed(() => {
    return inputMode.value === 'numeric' ? '[0-9-]*' : '[0-9.,-]*';
});

const applySanitizedToDom = () => {
    const comp = inputRef.value as any;
    const el: HTMLInputElement | null =
        comp?.$el?.querySelector?.('input') ?? null;

    if (!el) return;

    const s = sanitizeText(el.value);
    if (el.value !== s) el.value = s;
};

const innerValue = computed<string>({
    get() {
        return rawValue.value;
    },
    set(value: string) {
        const sanitized = sanitizeText(value);
        rawValue.value = sanitized;

        const trimmed = sanitized.trim();

        if (!trimmed) {
            emit('update:modelValue', null);
            errorMessage.value = '';
            return;
        }

        const num = Number(trimmed);
        if (Number.isNaN(num)) {
            emit('update:modelValue', null);
            return;
        }

        emit('update:modelValue', num);
    },
});

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

const readonlyClass = computed<string>(() => (props.readonly ? 'md-input-readonly' : ''));

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

    const text = sanitizeText(rawValue.value ?? '').trim();

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

    rawValue.value = sanitizeText(rawValue.value);
    requestAnimationFrame(applySanitizedToDom);

    if (props.required || props.min != null || props.max != null) {
        validate();
    }
};

const handleKeydown = (e: KeyboardEvent) => {
    if (props.readonly) {
        e.preventDefault();
        return;
    }

    if (!isValidKey(e, numericType.value)) {
        e.preventDefault();
    }
};

const handleBeforeInput = (e: InputEvent) => {
    if (props.readonly) {
        e.preventDefault();
        return;
    }

    const t = e.inputType ?? '';
    if (t.startsWith('delete') || t === 'historyUndo' || t === 'historyRedo') {
        return;
    }

    const data = (e as any).data as string | null;
    if (data == null) return;

    const el = e.target as HTMLInputElement | null;
    if (!el) return;

    const start = el.selectionStart ?? el.value.length;
    const end = el.selectionEnd ?? el.value.length;

    const next = el.value.slice(0, start) + data + el.value.slice(end);
    const sanitized = sanitizeText(next);

    if (sanitized !== next) {
        e.preventDefault();
    }
};

const handlePaste = (e: ClipboardEvent) => {
    if (props.readonly) return;

    const el = e.target as HTMLInputElement | null;
    if (!el) return;

    const paste = e.clipboardData?.getData('text') ?? '';
    const start = el.selectionStart ?? el.value.length;
    const end = el.selectionEnd ?? el.value.length;

    const next = el.value.slice(0, start) + paste + el.value.slice(end);
    innerValue.value = sanitizeText(next);

    requestAnimationFrame(applySanitizedToDom);
};

const handleDrop = (e: DragEvent) => {
    if (props.readonly) return;

    const el = e.target as HTMLInputElement | null;
    if (!el) return;

    const dropped = e.dataTransfer?.getData('text') ?? '';
    const start = el.selectionStart ?? el.value.length;
    const end = el.selectionEnd ?? el.value.length;

    const next = el.value.slice(0, start) + dropped + el.value.slice(end);
    innerValue.value = sanitizeText(next);

    requestAnimationFrame(applySanitizedToDom);
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
        comp.$el?.querySelector?.('input') ?? null;

    el?.focus();
};

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val == null ? '' : sanitizeText(String(val));
        requestAnimationFrame(applySanitizedToDom);
    },
    { immediate: true }
);

onMounted(() => {
    mdForm?.registerField(fieldKey, { validate, focus });
});

onBeforeUnmount(() => {
    mdForm?.unregisterField(fieldKey);
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
