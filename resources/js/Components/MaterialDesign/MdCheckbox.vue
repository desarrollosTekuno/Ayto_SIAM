<!-- resources/js/Components/MaterialDesign/MdCheckbox.vue -->
<template>
    <div class="w-full" :style="successColorVar">
        <v-checkbox
            v-model="innerValue"
            :id="id || name || undefined"
            :label="label"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :color="color"
            density="compact"
            hide-details="auto"
            @blur="handleBlur"
            @update:model-value="handleChange"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { useMdForm } from '@/utils/MdFormContext';

interface MdCheckboxProps {
    id?: string;
    name?: string;
    modelValue?: boolean;
    label?: string;
    required?: boolean;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string;
}

const props = withDefaults(defineProps<MdCheckboxProps>(), {
    id: '',
    name: '',
    modelValue: false,
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

const formContext = useMdForm();

// sincroniza cambios externos
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

const displayedError = computed(() => props.externalError || errorMessage.value);

const validate = () => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    if (props.required && rawValue.value !== true) {
        errorMessage.value = 'Debes marcar este campo';
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

const successColorVar = computed(() => {
    return props.color ? { '--md-checkbox-success-color': `var(--v-theme-${props.color})` } : {};
});

const fieldKey = computed(() => {
    if (props.name) return props.name;
    if (props.id) return props.id;
    return '';
});

onMounted(() => {
    if (formContext && fieldKey.value) {
        formContext.registerField(fieldKey.value, { validate });
    }
});

onBeforeUnmount(() => {
    if (formContext && fieldKey.value) {
        formContext.unregisterField(fieldKey.value);
    }
});

defineExpose({ validate });
</script>

<style scoped>
.md-input-success :deep(.v-selection-control__input) {
    opacity: 1 !important;
}
.md-input-success :deep(.v-selection-control__input > .v-icon) {
    color: var(--md-checkbox-success-color) !important;
}
.md-input-success :deep(.v-label) {
    color: var(--md-checkbox-success-color) !important;
}
</style>
