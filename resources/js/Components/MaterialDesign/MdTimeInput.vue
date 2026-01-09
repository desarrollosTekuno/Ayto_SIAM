<!-- resources/js/Components/Inputs/MdTimeInput.vue -->
<template>
    <div class="w-full h-18 px-2 py-2">
        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :disabled="readonly"
            transition="scale-transition"
            offset-y
        >
            <template #activator="{ props: activatorProps }">
                <v-text-field
                    ref="inputRef"
                    v-bind="activatorProps"
                    :model-value="displayValue"
                    :id="id"
                    :name="name"
                    :variant="variant"
                    :density="density"
                    :rounded="rounded"
                    :prepend-inner-icon="icon"
                    hide-details="auto"
                    :clearable="clearable"
                    :hint="computedHint"
                    :persistent-hint="persistentHint"
                    :readonly="true"
                    :error="!!displayedError"
                    :error-messages="displayedError"
                    :class="successClass"
                    @click:clear="handleClear"
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
                </v-text-field>
            </template>

            <v-time-picker
                v-model="tempValue"
                :format="format"
                :use-seconds="useSeconds"
                :readonly="readonly"
            >
                <template #actions>
                    <v-btn variant="text" @click="handleCancel">
                        Cancelar
                    </v-btn>
                    <v-btn variant="text" @click="handleConfirm">
                        Aceptar
                    </v-btn>
                </template>
            </v-time-picker>
        </v-menu>
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

type TimeFormat = 'ampm' | '24hr';
type ModelValue = string | null;

interface MdTimeInputProps {
    modelValue?: ModelValue;
    id?: string;
    name?: string;
    label?: string;
    tooltip?: string;
    icon?: string;
    required?: boolean;
    helper?: string;
    helperPersistent?: boolean;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    density?: Density;
    variant?: Variant;
    rounded?: boolean | string | number;
    clearable?: boolean;
    format?: TimeFormat;
    useSeconds?: boolean;
}

const props = withDefaults(defineProps<MdTimeInputProps>(), {
    modelValue: null,
    id: undefined,
    name: undefined,
    label: '',
    tooltip: '',
    icon: 'mdi-clock-time-four-outline',
    required: false,
    helper: '',
    helperPersistent: false,
    readonly: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    variant: 'outlined',
    rounded: 'lg',
    clearable: true,
    format: '24hr',
    useSeconds: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const rawValue = ref<ModelValue>(props.modelValue ?? null);
const tempValue = ref<ModelValue>(props.modelValue ?? null);
const menu = ref(false);
const errorMessage = ref<string>('');
const touched = ref(false);

const inputRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdTimeInput_${props.id || instance?.uid || Math.random().toString(36)}`;

watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val ?? null;
        if (!menu.value) {
            tempValue.value = rawValue.value;
        }
    },
    { immediate: true }
);

watch(
    () => menu.value,
    (open) => {
        if (open) {
            tempValue.value = rawValue.value ?? null;
        }
    }
);

const innerValue = computed<ModelValue>({
    get() {
        return rawValue.value;
    },
    set(value) {
        rawValue.value = value;
        emit('update:modelValue', value);
    },
});

const displayValue = computed<string>(() => {
    return innerValue.value ?? '';
});

const displayedError = computed<string>(() => {
    return props.externalError || errorMessage.value;
});

/** Helper: no lo mostramos si hay error */
const computedHint = computed<string>(() => {
    if (displayedError.value) return '';
    return (props.helper ?? '').trim();
});

const persistentHint = computed<boolean>(() => {
    return !!computedHint.value && !!props.helperPersistent;
});

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    const v = innerValue.value;

    if (props.required && !v) {
        errorMessage.value = 'Este campo es obligatorio';
        return false;
    }

    errorMessage.value = '';
    return true;
};

const handleBlur = () => {
    validate();
};

const handleClear = () => {
    if (props.readonly) return;
    innerValue.value = null;
    tempValue.value = null;
    errorMessage.value = '';
};

const handleCancel = () => {
    tempValue.value = innerValue.value;
    menu.value = false;
};

const handleConfirm = () => {
    innerValue.value = tempValue.value;
    menu.value = false;
    validate();
};

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

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
