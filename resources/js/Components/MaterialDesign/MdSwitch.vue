<!-- resources/js/Components/MaterialDesign/MdSwitch.vue -->
<template>
    <div class="w-full">
        <v-switch
            ref="switchRef"
            v-model="innerValue"
            :label="label"
            :id="id"
            :name="name"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :color="color"
            density="compact"
            class="px-2"
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

interface MdSwitchProps {
    modelValue?: boolean;
    id?: string;
    name?: string;
    label?: string;
    required?: boolean;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string;
}

const props = withDefaults(defineProps<MdSwitchProps>(), {
    modelValue: false,
    id: undefined,
    name: undefined,
    label: '',
    required: false,
    readonly: false,
    externalError: '',
    showSuccessState: true,
    color: undefined,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const rawValue = ref<boolean>(props.modelValue);
const errorMessage = ref('');
const touched = ref(false);

// ref al v-switch para poder hacer focus()
const switchRef = ref<any | null>(null);

// MdFormContext
const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdSwitch_${props.id || instance?.uid || Math.random().toString(36)}`;

// Sincronizar cambios externos
watch(
    () => props.modelValue,
    (val) => (rawValue.value = val),
    { immediate: true }
);

const innerValue = computed({
    get: () => rawValue.value,
    set: (v: boolean) => {
        rawValue.value = v;
        emit('update:modelValue', v);
    },
});

const displayedError = computed(
    () => props.externalError || errorMessage.value
);

const validate = () => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    if (props.required && rawValue.value !== true) {
        errorMessage.value = 'Debes activar este campo';
        return false;
    }

    errorMessage.value = '';
    return true;
};

const handleBlur = () => validate();

const handleChange = () => {
    if (!touched.value) touched.value = true;
    if (props.required) validate();
};

const focus = () => {
    const comp = switchRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLInputElement | null =
        comp.$el?.querySelector?.('input[type="checkbox"]') ?? null;

    el?.focus();
};

const successClass = computed(() => {
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

defineExpose({ validate, focus });
</script>

<style scoped>
.md-input-success :deep(.v-selection-control__input) {
    color: #16a34a !important;
}
.md-input-success :deep(.v-label) {
    color: #16a34a !important;
}
</style>
