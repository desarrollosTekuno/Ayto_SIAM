<!-- resources/js/Components/MaterialDesign/MdToggle.vue -->
<template>
    <div class="w-full">
        <div class="flex flex-col gap-1" :class="successClass">
            <label v-if="label" class="text-sm text-gray-700">
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
            </label>

            <v-btn-toggle
                ref="toggleRef"
                v-model="innerValue"
                class="md-toggle-group"
                :id="id"
                :name="name"
                :mandatory="true"
                :rounded="rounded"
                :disabled="readonly"
                :density="density"
            >
                <v-btn
                    :value="leftValue"
                    variant="tonal"
                    size="small"
                    :color="innerValue === leftValue ? color : undefined"
                >
                    {{ leftLabel }}
                </v-btn>

                <v-btn
                    :value="rightValue"
                    variant="tonal"
                    size="small"
                    :color="innerValue === rightValue ? color : undefined"
                >
                    {{ rightLabel }}
                </v-btn>
            </v-btn-toggle>

            <p v-if="displayedError" class="mt-1 text-xs text-red-600">
                {{ displayedError }}
            </p>
            <p v-else-if="computedHint" class="mt-1 text-xs text-gray-500">
                {{ computedHint }}
            </p>
        </div>
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

type Density = 'default' | 'comfortable' | 'compact';

type ToggleValue = string | number | boolean | null | undefined;

interface MdToggleProps {
    modelValue?: ToggleValue;
    id?: string;
    name?: string;
    label?: string;
    tooltip?: string;
    leftLabel?: string;
    rightLabel?: string;
    leftValue?: ToggleValue;
    rightValue?: ToggleValue;
    required?: boolean;
    readonly?: boolean;
    helper?: string;
    helperPersistent?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string;
    density?: Density;
    rounded?: boolean | string | number;
}

const props = withDefaults(defineProps<MdToggleProps>(), {
    modelValue: null,
    id: undefined,
    name: undefined,
    label: '',
    tooltip: '',
    leftLabel: 'No',
    rightLabel: 'Sí',
    leftValue: false,
    rightValue: true,
    required: false,
    readonly: false,
    helper: '',
    helperPersistent: false,
    externalError: '',
    showSuccessState: true,
    color: 'primary',
    density: 'compact',
    rounded: 'lg',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ToggleValue): void;
}>();

const rawValue = ref<ToggleValue>(props.modelValue);
const errorMessage = ref('');
const touched = ref(false);

const toggleRef = ref<any | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdToggle_${props.id || instance?.uid || Math.random().toString(36)}`;

watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val;
    },
    { immediate: true }
);

const innerValue = computed<ToggleValue>({
    get() {
        return rawValue.value;
    },
    set(v) {
        if (props.readonly) return;
        rawValue.value = v;
        emit('update:modelValue', v);
        if (!touched.value) touched.value = true;
        if (props.required) validate();
    },
});

const displayedError = computed(
    () => props.externalError || errorMessage.value
);

const isEmpty = (v: ToggleValue): boolean => {
    return v === null || v === undefined || v === '';
};

const validate = () => {
    if (props.readonly) {
        errorMessage.value = '';
        return true;
    }

    touched.value = true;

    if (props.required && isEmpty(rawValue.value)) {
        errorMessage.value = 'Debes seleccionar una opción';
        return false;
    }

    errorMessage.value = '';
    return true;
};

const computedHint = computed(() => {
    if (displayedError.value) return '';
    const txt = (props.helper ?? '').trim();
    if (!txt) return '';
    if (props.helperPersistent) return txt;
    return touched.value ? txt : '';
});

const successClass = computed(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-toggle-success';
});

const focus = () => {
    const comp = toggleRef.value as any;
    if (!comp) return;

    if (typeof comp.focus === 'function') {
        comp.focus();
        return;
    }

    const el: HTMLButtonElement | null =
        comp.$el?.querySelector?.('button') ?? null;

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
.md-toggle-group {
    border-radius: 9999px;
    padding: 2px;
}

.md-toggle-success :deep(.v-btn--active) {
    box-shadow: 0 0 0 1px #16a34a33;
}

.md-toggle-success label {
    color: #16a34a !important;
}
</style>
