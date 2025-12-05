<!-- resources/js/Components/MaterialDesign/MdEmailInput.vue -->
<template>
    <MdTextInput
        ref="baseRef"
        :model-value="modelValue"
        :label="label"
        :icon="icon || 'mdi-email-outline'"
        :type="'email'"
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
        :allowed="'email'"
        :pattern="pattern || defaultPattern"
        :show-success-state="showSuccessState"
        :density="density"
        :rounded="rounded"
        @update:model-value="onUpdate"
    />
</template>

<script setup lang="ts">
// @ts-nocheck
import { ref } from 'vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';

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

interface MdEmailInputProps {
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

const props = withDefaults(defineProps<MdEmailInputProps>(), {
    modelValue: '',
    label: '',
    icon: '',
    required: false,
    variant: 'outlined',
    clearable: true,
    minLength: null,
    maxLength: 120,
    counter: false,
    helper: '',
    readonly: false,
    externalError: '',
    pattern: null,
    showSuccessState: true,
    density: 'compact',
    rounded: 'xl',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void;
}>();


const defaultPattern: RegExp =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const baseRef = ref<InstanceType<typeof MdTextInput> | null>(null);

const onUpdate = (value: unknown) => {
    emit('update:modelValue', (value ?? '') as ModelValue);
};

// exponer validate()
const validate = (): boolean => {
    return baseRef.value?.validate() ?? true;
};

defineExpose({ validate });
</script>
