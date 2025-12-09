<!-- resources/js/Components/MaterialDesign/MdPhoneInput.vue -->
<template>
    <MdTextInput
        ref="baseRef"
        :model-value="modelValue"
        :label="label"
        :id="id"
        :name="name"
        :icon="icon || 'mdi-phone-outline'"
        :type="'text'"
        :required="required"
        :variant="variant"
        :clearable="clearable"
        :uppercase="false"
        :min-length="minLength"
        :max-length="maxLength"
        :counter="counter"
        :helper="helper"
        :readonly="readonly"
        :external-error="externalError"
        :allowed="'phone'"
        :pattern="pattern || defaultPattern"
        :show-success-state="showSuccessState"
        :density="density"
        :rounded="rounded"
        @update:model-value="onUpdate"
    />
</template>

<script setup lang="ts">
// @ts-nocheck
import {
    ref,
    onMounted,
    onBeforeUnmount,
    getCurrentInstance,
} from 'vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';
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

interface MdPhoneInputProps {
    id?: string;
    name?: string;
    modelValue?: ModelValue;
    label?: string;
    icon?: string;
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
}

const props = withDefaults(defineProps<MdPhoneInputProps>(), {
    id: undefined,
    name: undefined,
    modelValue: '',
    label: '',
    icon: '',
    required: false,
    variant: 'outlined',
    clearable: true,
    minLength: 10,
    maxLength: 15,
    counter: false,
    helper: '',
    readonly: false,
    externalError: '',
    pattern: null,
    showSuccessState: true,
    density: 'compact',
    rounded: 'sm',
});

const defaultPattern: RegExp = /^[0-9+\-\s]{10,20}$/;

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();

const baseRef = ref<InstanceType<typeof MdTextInput> | null>(null);

// MdFormContext
const mdForm = useMdForm();
const instance = getCurrentInstance();

// Si el usuario no da "name", se genera uno automático
const fieldKey =
    props.name ||
    `MdPhoneInput_${props.id || instance?.uid || Math.random().toString(36)}`;

const onUpdate = (value: unknown) => {
    emit('update:modelValue', (value ?? '') as ModelValue);
};

const validate = (): boolean => baseRef.value?.validate() ?? true;

const focus = () => {
    const comp = baseRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') return comp.focus();

    const el: HTMLInputElement | null =
        comp.$el?.querySelector?.('input') ?? null;

    el?.focus();
};

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
