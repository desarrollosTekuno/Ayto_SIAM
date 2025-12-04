<!-- resources/js/Components/MaterialDesign/MdToggle.vue -->
<template>
    <div class="w-full max-w-xs">
        <div class="flex flex-col gap-1" :class="successClass">
            <label v-if="label" class="text-sm text-gray-700">
                {{ label }}
            </label>

            <v-btn-toggle
                v-model="innerValue"
                class="md-toggle-group"
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
            <p v-else-if="helper" class="mt-1 text-xs text-gray-500">
                {{ helper }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';

type Density = 'default' | 'comfortable' | 'compact';

type ToggleValue = string | number | boolean | null | undefined;

interface MdToggleProps {
    modelValue?: ToggleValue;
    label?: string;

    leftLabel?: string;
    rightLabel?: string;
    leftValue?: ToggleValue;
    rightValue?: ToggleValue;

    required?: boolean;
    readonly?: boolean;
    helper?: string;
    externalError?: string;
    showSuccessState?: boolean;
    color?: string;
    density?: Density;
    rounded?: boolean | string | number;
}

const props = withDefaults(defineProps<MdToggleProps>(), {
    modelValue: null,
    label: '',
    leftLabel: 'No',
    rightLabel: 'Sí',
    leftValue: false,
    rightValue: true,
    required: false,
    readonly: false,
    helper: '',
    externalError: '',
    showSuccessState: true,
    color: 'primary',
    density: 'compact',
    rounded: 'xl',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: ToggleValue): void;
}>();

const rawValue = ref<ToggleValue>(props.modelValue);
const errorMessage = ref('');
const touched = ref(false);

// Sincronizar cambios externos
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

const displayedError = computed(() => props.externalError || errorMessage.value);

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

const successClass = computed(() => {
    if (!props.showSuccessState) return '';
    if (!touched.value) return '';
    if (displayedError.value) return '';
    return 'md-toggle-success';
});

defineExpose({
    validate,
});
</script>

<style scoped>
.md-toggle-group {
    border-radius: 9999px;
    padding: 2px;
    /* sin borde */
}

.md-toggle-success :deep(.v-btn--active) {
    box-shadow: 0 0 0 1px #16a34a33;
}

.md-toggle-success label {
    color: #16a34a !important;
}

</style>
