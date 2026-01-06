<!-- resources/js/Components/MaterialDesign/MdCheckbox.vue -->
<template>
    <div class="w-full h-18 py-2 px-2" :style="successColorVar">
        <v-checkbox
            v-model="innerValue"
            :id="id || name || undefined"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :color="color"
            :messages="computedHint"
            :persistent-hint="persistentHint"
            density="compact"
            hide-details="auto"
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
        </v-checkbox>
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
    tooltip?: string;
    required?: boolean;
    helper?: string;
    helperPersistent?: boolean;
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
    tooltip: '',
    required: false,
    helper: '',
    helperPersistent: false,
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

/** Helper: no lo mostramos si hay error */
const computedHint = computed(() => {
    if (displayedError.value) return undefined;
    const txt = (props.helper ?? '').trim();
    return txt ? txt : undefined;
});

const persistentHint = computed(() => {
    return !!computedHint.value && !!props.helperPersistent;
});

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
    return props.color
        ? { '--md-checkbox-success-color': `var(--v-theme-${props.color})` }
        : {};
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
