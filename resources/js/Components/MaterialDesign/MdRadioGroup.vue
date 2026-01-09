<!-- resources/js/Components/MaterialDesign/MdRadioGroup.vue -->
<template>
    <div class="w-full" :style="successColorVar">
        <v-radio-group
            ref="groupRef"
            v-model="innerValue"
            :id="id"
            :name="name"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="successClass"
            :messages="computedHint"
            :persistent-hint="persistentHint"
            color="transparent"
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
import {
    ref,
    computed,
    watch,
    onMounted,
    onBeforeUnmount,
    getCurrentInstance,
} from 'vue';
import { useMdForm } from '@/utils/MdFormContext';

interface MdRadioGroupProps {
    modelValue?: string | number | boolean | null;
    label?: string;
    tooltip?: string;
    id?: string;
    name?: string;
    items?: Array<any>;
    itemTitle?: string;
    itemValue?: string;
    required?: boolean;
    helper?: string;
    helperPersistent?: boolean;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string;
}

const props = withDefaults(defineProps<MdRadioGroupProps>(), {
    modelValue: null,
    label: '',
    tooltip: '',
    id: '',
    name: '',
    items: () => [],
    itemTitle: 'label',
    itemValue: 'value',
    required: false,
    helper: '',
    helperPersistent: false,
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

const groupRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdRadioGroup_${props.id || instance?.uid || Math.random().toString(36)}`;

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

    if (
        props.required &&
        (rawValue.value === null ||
            rawValue.value === undefined ||
            rawValue.value === '')
    ) {
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

const successColorVar = computed(() => {
    return props.color
        ? { '--md-radio-success-color': `var(--v-theme-${props.color})` }
        : {};
});

const focus = () => {
    const comp = groupRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLInputElement | null =
        comp.$el?.querySelector?.('input[type="radio"]') ?? null;

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

.md-input-success :deep(.v-selection-control__input .v-icon) {
    color: var(--md-radio-success-color) !important;
}
</style>
