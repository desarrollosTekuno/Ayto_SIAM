<!-- resources/js/Components/MaterialDesign/MdSlider.vue -->
<template>
    <div class="w-full">
        <v-slider
            ref="sliderRef"
            v-model="innerValue"
            :id="id"
            :name="name"
            :min="min"
            :max="max"
            :step="step"
            :readonly="readonly"
            :disabled="disabled"
            :show-ticks="showTicks"
            :ticks="ticks"
            :thumb-label="thumbLabel"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :density="density"
            :color="color"
            @blur="handleBlur"
            @update:model-value="handleChange"
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
        </v-slider>

        <p v-if="computedHint" class="mt-1 text-xs text-gray-500">
            {{ computedHint }}
        </p>
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

interface MdSliderProps {
    modelValue?: number | null;
    label?: string;
    tooltip?: string;
    id?: string;
    name?: string;
    required?: boolean;
    readonly?: boolean;
    disabled?: boolean;
    min?: number;
    max?: number;
    step?: number;
    helper?: string;
    helperPersistent?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    density?: 'comfortable' | 'compact' | 'default';
    color?: string;
    showTicks?: boolean | 'always';
    ticks?: number[] | Record<number, string>;
    thumbLabel?: boolean | 'always';
}

const props = withDefaults(defineProps<MdSliderProps>(), {
    modelValue: null,
    label: '',
    tooltip: '',
    id: undefined,
    name: undefined,
    required: false,
    readonly: false,
    disabled: false,
    min: 0,
    max: 100,
    step: 1,
    helper: '',
    helperPersistent: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    color: 'primary',
    showTicks: false,
    thumbLabel: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | null): void;
}>();

const rawValue = ref<number | null>(props.modelValue);
const innerValue = ref<number | null>(props.modelValue);
const isDirty = ref(false);

const sliderRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdSlider_${props.id || instance?.uid || Math.random().toString(36)}`;

watch(
    () => props.modelValue,
    (value) => {
        rawValue.value = value;
        innerValue.value = value;
    }
);

const validationError = computed(() => {
    if (props.externalError) return props.externalError;

    const value = rawValue.value;

    if (props.required && (value === null || value === undefined)) {
        return 'Este campo es requerido.';
    }

    if (value !== null && value !== undefined) {
        if (value < props.min) return `El valor minimo es ${props.min}.`;
        if (value > props.max) return `El valor maximo es ${props.max}.`;
    }

    return '';
});

const displayedError = computed(() => (isDirty.value ? validationError.value : ''));

const successClass = computed(() => {
    return props.showSuccessState && isDirty.value && !displayedError.value
        ? 'md-input-success'
        : '';
});

const computedHint = computed(() => {
    if (displayedError.value) return '';
    const txt = (props.helper ?? '').trim();
    if (!txt) return '';
    if (props.helperPersistent) return txt;
    return isDirty.value ? txt : '';
});

const handleBlur = () => {
    isDirty.value = true;
};

const handleChange = (value: number | null) => {
    isDirty.value = true;
    rawValue.value = value;
    innerValue.value = value;
    emit('update:modelValue', value);
};

const validate = (): boolean => {
    isDirty.value = true;
    return validationError.value === '';
};

const focus = () => {
    const comp = sliderRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLInputElement | null =
        comp.$el?.querySelector?.('input[type="range"]') ??
        comp.$el?.querySelector?.('input') ??
        null;

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
.md-input-success :deep(.v-label) {
    color: #16a34a !important;
}
</style>
