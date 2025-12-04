<!-- resources/js/Components/MaterialDesign/MdSwitch.vue -->
<template>
    <div class="w-full max-w-xs">
        <v-switch
            v-model="innerValue"
            :label="label"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :color="color"
            density="compact"
            @blur="handleBlur"
            @update:model-value="handleChange"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

interface MdSwitchProps {
    modelValue?: boolean;
    label?: string;
    required?: boolean;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string; // nuevo prop
}

const props = withDefaults(defineProps<MdSwitchProps>(), {
    modelValue: false,
    label: '',
    required: false,
    readonly: false,
    externalError: '',
    showSuccessState: true,
    color: undefined, // usa el default de Vuetify si no se envía
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
}>();

const rawValue = ref<boolean>(props.modelValue);
const errorMessage = ref('');
const touched = ref(false);

// Sincronizar cambios externos
watch(
    () => props.modelValue,
    (val) => rawValue.value = val,
    { immediate: true }
);

const innerValue = computed({
    get: () => rawValue.value,
    set: (v: boolean) => {
        rawValue.value = v;
        emit('update:modelValue', v);
    }
});

const displayedError = computed(() => props.externalError || errorMessage.value);

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

const successClass = computed(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-input-success';
});

defineExpose({ validate });
</script>

<style scoped>
.md-input-success :deep(.v-selection-control__input) {
    color: #16a34a !important;
}
.md-input-success :deep(.v-label) {
    color: #16a34a !important;
}
</style>
