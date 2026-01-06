<!-- resources/js/Components/MaterialDesign/MdSwitch.vue -->
<template>
    <div class="w-full">
        <v-switch
            ref="switchRef"
            v-model="innerValue"
            :id="id"
            :name="name"
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
        </v-switch>
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
    tooltip?: string;
    required?: boolean;
    helper?: string;
    helperPersistent?: boolean;
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

const switchRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdSwitch_${props.id || instance?.uid || Math.random().toString(36)}`;

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

onMounted(() => {
    mdForm?.registerField(fieldKey, { validate, focus });
});

onBeforeUnmount(() => {
    mdForm?.unregisterField(fieldKey);
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
