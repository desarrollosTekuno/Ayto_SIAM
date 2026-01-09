<!-- resources/js/Components/MaterialDesign/MdDatePicker.vue -->
<template>
    <div class="w-full h-18 py-2 px-2">
        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
        >
            <template #activator="{ props: menuProps }">
                <v-text-field
                    ref="inputRef"
                    v-bind="menuProps"
                    v-model="displayText"
                    :id="id"
                    :name="name"
                    :prepend-inner-icon="icon"
                    :density="density"
                    :variant="variant"
                    :rounded="rounded"
                    :clearable="clearable"
                    :hint="computedHint"
                    :persistent-hint="persistentHint"
                    :readonly="true"
                    :error="!!displayedError"
                    :error-messages="displayedError"
                    :class="successClass"
                    autocomplete="off"
                    hide-details="auto"
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

            <v-date-picker
                v-model="innerValue"
                :multiple="computedMultiple"
                :min="min"
                :max="max"
                :locale="locale"
                :color="color"
                show-adjacent-months
                @update:model-value="handleSelect"
            />
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

type SingleDate = string | Date | null;
type MultiDate = (string | Date)[];
type ModelValue = SingleDate | MultiDate;

interface MdDatePickerProps {
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
    min?: string | null;
    max?: string | null;
    multiple?: boolean;
    range?: boolean;
    locale?: string;
    color?: string;
}

const props = withDefaults(defineProps<MdDatePickerProps>(), {
    modelValue: null,
    label: '',
    tooltip: '',
    id: undefined,
    name: undefined,
    icon: 'mdi-calendar',
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
    min: null,
    max: null,
    multiple: false,
    range: false,
    locale: 'es-MX',
    color: 'primary',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const menu = ref(false);
const rawValue = ref<ModelValue>(
    props.modelValue ?? (props.range || props.multiple ? [] : null)
);
const errorMessage = ref('');
const touched = ref(false);

const inputRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name || `MdDatePicker_${instance?.uid ?? Math.random().toString(36)}`;

watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val ?? (props.range || props.multiple ? [] : null);
    },
    { immediate: true }
);

const computedMultiple = computed(() => {
    if (props.range) return 'range';
    if (props.multiple) return true;
    return false;
});

const innerValue = computed<ModelValue>({
    get() {
        return rawValue.value;
    },
    set(value) {
        rawValue.value = value;
        emit('update:modelValue', value);
    },
});

const formatDate = (value: string | Date): string => {
    if (!value) return '';
    const d = value instanceof Date ? value : new Date(value);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleDateString(props.locale || 'es-MX');
};

const displayText = computed<string>({
    get() {
        const v = rawValue.value;
        if (!v) return '';

        if (props.range || props.multiple) {
            const arr = Array.isArray(v) ? v : [v];
            const formatted = arr
                .map((item) => formatDate(item))
                .filter(Boolean);

            if (props.range && formatted.length === 2) {
                return `${formatted[0]} - ${formatted[1]}`;
            }

            return formatted.join(', ');
        }

        return formatDate(v as SingleDate);
    },
    set() {},
});

const displayedError = computed(() => {
    return props.externalError || errorMessage.value;
});

/** Helper: no lo mostramos si hay error */
const computedHint = computed<string>(() => {
    if (displayedError.value) return '';
    return (props.helper ?? '').trim();
});

/** Controla si el helper es persistente o solo al focus */
const persistentHint = computed<boolean>(() => {
    return !!computedHint.value && !!props.helperPersistent;
});

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;
    const v = rawValue.value;

    if (!props.required) {
        errorMessage.value = '';
        return true;
    }

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

    errorMessage.value = '';
    return true;
};

const handleSelect = (value: any) => {
    rawValue.value = value;
    emit('update:modelValue', value);

    if (!props.range && !props.multiple) {
        menu.value = false;
    } else if (props.range) {
        const arr = Array.isArray(value) ? value : [];
        if (arr.length === 2) {
            menu.value = false;
        }
    }

    validate();
};

const focus = () => {
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
