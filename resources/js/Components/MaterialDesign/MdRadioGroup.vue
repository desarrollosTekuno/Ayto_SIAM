<!-- resources/js/Components/MaterialDesign/MdRadioGroup.vue -->
<template>
    <div class="w-full max-w-xs" :style="successColorVar">
        <v-radio-group
            v-model="innerValue"
            :label="label"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            color="transparent"
            density="compact"
            hide-details="auto"
            @blur="handleBlur"
            @update:model-value="handleChange"
        >
            <v-radio
                v-for="item in items"
                :key="item[itemValue]"
                :label="item[itemTitle]"
                :value="item[itemValue]"
                :color="color"
            />
        </v-radio-group>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface MdRadioGroupProps {
    modelValue?: string | number | boolean | null;
    label?: string;
    items?: Array<any>;
    itemTitle?: string;
    itemValue?: string;
    required?: boolean;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string;
}

const props = withDefaults(defineProps<MdRadioGroupProps>(), {
    modelValue: null,
    label: '',
    items: () => [],
    itemTitle: 'label',
    itemValue: 'value',
    required: false,
    readonly: false,
    externalError: '',
    showSuccessState: true,
    color: undefined,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void;
}>();

const rawValue = ref(props.modelValue);
const errorMessage = ref('');
const touched = ref(false);

// Sincroniza cambios desde fuera
watch(
    () => props.modelValue,
    (val) => (rawValue.value = val),
    { immediate: true }
);

const innerValue = computed({
    get: () => rawValue.value,
    set: (v) => {
        rawValue.value = v;
        emit('update:modelValue', v);
    },
});

const displayedError = computed(() => props.externalError || errorMessage.value);

const validate = () => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    if (props.required && (rawValue.value === null || rawValue.value === undefined || rawValue.value === '')) {
        errorMessage.value = 'Debes seleccionar una opcion';
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

// color para el estado de exito
const successColorVar = computed(() => {
    return props.color
        ? { '--md-radio-success-color': `var(--v-theme-${props.color})` }
        : {};
});

defineExpose({ validate });
</script>

<style scoped>
.md-input-success :deep(.v-label) {
    color: #16a34a !important;
}

.md-input-success :deep(.v-selection-control__input .v-icon) {
    color: var(--md-radio-success-color) !important;
}
</style>
