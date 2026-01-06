<!-- resources/js/Components/MaterialDesign/MdTextareaInput.vue -->
<template>
    <div class="w-full px-2 py-2">
        <v-textarea
            ref="textareaRef"
            v-model="innerValue"
            :id="id"
            :name="name"
            :prepend-inner-icon="icon"
            :density="density"
            :variant="variant"
            :rows="rows"
            :max-rows="maxRows ?? undefined"
            :auto-grow="autoGrow"
            :rounded="rounded"
            :error="!!displayedError"
            :error-messages="displayedError"
            :clearable="clearable"
            :maxlength="maxLength ?? undefined"
            :counter="counter ? (maxLength ?? undefined) : undefined"
            :class="successClass"
            :hint="helper"
            :persistent-hint="!!helper"
            :readonly="readonly"
            autocomplete="off"
            @keydown="handleKeydown"
            @blur="handleBlur"
        >
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
        </v-textarea>
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

interface MdTextareaProps {
    modelValue?: ModelValue;
    id?: string;
    name?: string;
    label?: string;
    tooltip?: string;
    icon?: string;
    required?: boolean;
    variant?: Variant;
    clearable?: boolean;
    uppercase?: boolean;
    minLength?: number | null;
    maxLength?: number | null;
    counter?: boolean;
    helper?: string;
    readonly?: boolean;
    externalError?: string;
    allowed?: AllowedType;
    pattern?: string | RegExp | null;
    showSuccessState?: boolean;
    density?: Density;
    rounded?: boolean | string | number;
    rows?: number;
    maxRows?: number | null;
    autoGrow?: boolean;
}

const props = withDefaults(defineProps<MdTextareaProps>(), {
    modelValue: '',
    id: undefined,
    name: undefined,
    label: '',
    tooltip: '',
    icon: '',
    required: false,
    variant: 'outlined',
    clearable: false,
    uppercase: false,
    minLength: null,
    maxLength: null,
    counter: false,
    helper: '',
    readonly: false,
    externalError: '',
    allowed: 'any',
    pattern: null,
    showSuccessState: true,
    density: 'comfortable',
    rounded: 'lg',
    rows: 3,
    maxRows: null,
    autoGrow: true,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const errorMessage = ref<string>('');
const touched = ref(false);

const textareaRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdTextarea_${props.id || instance?.uid || Math.random().toString(36)}`;

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
                s = toUpper(s, props.uppercase ?? false);

                if (
                    typeof props.maxLength === 'number' &&
                    props.maxLength > 0
                ) {
                    s = s.slice(0, props.maxLength);
                }

                newValue = s;
            }
        } else {
            newValue = sanitizeByType(
                newValue,
                type,
                props.uppercase ?? false,
                props.maxLength ?? undefined
            );
        }

        emit('update:modelValue', newValue);
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
    const type: AllowedType = props.allowed ?? 'any';
    if (!isValidKey(e, type)) {
        e.preventDefault();
    }
};

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

const focus = () => {
    const comp = textareaRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLTextAreaElement | null =
        comp.$el?.querySelector?.('textarea') ?? null;

    el?.focus();
};

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

.md-input-success :deep(.v-field__prepend-inner .v-icon) {
    color: #16a34a !important;
}
</style>
