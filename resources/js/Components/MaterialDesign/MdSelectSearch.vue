<!-- resources/js/Components/MaterialDesign/MdSelectSearch.vue -->
<template>
    <div class="w-full">
        <v-autocomplete
            ref="selectRef"
            v-model="innerValue"
            :items="items"
            :label="label"
            :id="id"
            :name="name"
            :item-title="itemTitle"
            :item-value="itemValue"
            :multiple="multiple"
            :return-object="returnObject"
            :density="density"
            :variant="variant"
            :rounded="rounded"
            :clearable="clearable"
            :hint="helper"
            :persistent-hint="!!helper"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :chips="chips && multiple"
            :menu-props="menuProps"
            autocomplete="off"
            @blur="handleBlur"
            @update:model-value="handleChange"
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

type Primitive = string | number | boolean | null | undefined;
type SingleValue = Primitive | Record<string, any> | null;
type MultiValue  = SingleValue[];
type ModelValue  = SingleValue | MultiValue;

interface MdSelectSearchProps {
    modelValue?: ModelValue;
    label?: string;
    id?: string;
    name?: string;
    items?: any[];
    itemTitle?: string;
    itemValue?: string;
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
    multiple?: boolean;
    returnObject?: boolean;
    chips?: boolean;
    minSelected?: number | null;
    maxSelected?: number | null;
}

const props = withDefaults(defineProps<MdSelectSearchProps>(), {
    modelValue: null,
    label: '',
    id: '',
    name: '',
    items: () => [],
    itemTitle: 'label',
    itemValue: 'value',
    icon: '',
    required: false,
    helper: '',
    readonly: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    variant: 'outlined',
    rounded: 'xl',
    clearable: true,
    multiple: false,
    returnObject: false,
    chips: false,
    minSelected: null,
    maxSelected: null,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const rawValue = ref<ModelValue>(
    props.modelValue ?? (props.multiple ? [] : null)
);
const errorMessage = ref<string>('');
const touched = ref(false);

// ref al v-autocomplete para poder hacer focus()
const selectRef = ref<any | null>(null);

// MdFormContext
const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdSelectSearch_${props.id || instance?.uid || Math.random().toString(36)}`;

// sync externo
watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val ?? (props.multiple ? [] : null);
    },
    { immediate: true }
);

// v-model interno
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

// props del menu
const menuProps = computed(() => ({
    maxHeight: 320,
}));

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;
    const v = rawValue.value;

    if (props.multiple) {
        const arr = Array.isArray(v) ? v : [];

        if (props.required && arr.length === 0) {
            errorMessage.value = 'Este campo es obligatorio';
            return false;
        }

        if (props.minSelected != null && arr.length < props.minSelected) {
            errorMessage.value = `Selecciona al menos ${props.minSelected} opciones`;
            return false;
        }

        if (props.maxSelected != null && arr.length > props.maxSelected) {
            errorMessage.value = `Selecciona máximo ${props.maxSelected} opciones`;
            return false;
        }

        errorMessage.value = '';
        return true;
    }

    if (props.required) {
        if (
            v == null ||
            v === '' ||
            (typeof v === 'number' && Number.isNaN(v))
        ) {
            errorMessage.value = 'Este campo es obligatorio';
            return false;
        }
    }

    errorMessage.value = '';
    return true;
};

const handleBlur = () => {
    validate();
};

const handleChange = () => {
    if (!touched.value) touched.value = true;
    if (
        props.required ||
        props.multiple ||
        props.minSelected != null ||
        props.maxSelected != null
    ) {
        validate();
    }
};

const focus = () => {
    const comp = selectRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLInputElement | null =
        comp.$el?.querySelector?.('input') ??
        comp.$el?.querySelector?.('[role="combobox"]') ??
        null;

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

.md-input-success :deep(.v-field__prepend-inner .v-icon),
.md-input-success :deep(.v-select__selection-text) {
    color: #16a34a !important;
}
</style>
