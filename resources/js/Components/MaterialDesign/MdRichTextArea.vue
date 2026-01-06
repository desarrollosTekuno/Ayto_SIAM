<!-- resources/js/Components/MaterialDesign/MdRichTextArea.vue-->
<template>
    <div class="w-full mb-2 py-2 px-2">
        <label
            v-if="label"
            class="block mb-1 text-sm font-medium text-gray-700"
            :for="id || name"
        >
            <span class="inline-flex items-center gap-1">
                <span v-if="required" class="text-red-500 font-bold">*</span>
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

        <div
            class="md-rich-text quill-comentarios"
            :class="{
                'md-error': !!displayedError,
                'md-success': showSuccessState && !displayedError && hasValue
            }"
        >
            <QuillEditor
                ref="editorRef"
                v-model:content="innerValue"
                content-type="html"
                theme="snow"
                :toolbar="toolbar"
                :placeholder="placeholder"
                :read-only="readonly"
                class="w-full"
                @blur="handleBlur"
            />
        </div>

        <input
            v-if="name"
            type="hidden"
            :name="name"
            :id="id || name"
            :value="plainText"
        />

        <p v-if="helper" class="mt-1 text-xs text-gray-500">
            {{ helper }}
        </p>

        <p v-if="displayedError" class="mt-1 text-xs text-red-600">
            {{ displayedError }}
        </p>
    </div>
</template>

<script setup lang="ts">
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {
    ref,
    computed,
    watch,
    onMounted,
    onBeforeUnmount,
    getCurrentInstance,
} from 'vue';
import { useMdForm } from '@/utils/MdFormContext';

type MdToolbarPreset = 'minimal' | 'essential' | 'full' | '';

interface MdRichTextProps {
    modelValue?: string;
    label?: string;
    tooltip?: string;
    id?: string;
    name?: string;
    required?: boolean;
    minLength?: number;
    maxLength?: number;
    helper?: string;
    placeholder?: string;
    readonly?: boolean;
    externalError?: string;
    showSuccessState?: boolean;
    toolbar?: MdToolbarPreset | any;
}

const props = withDefaults(defineProps<MdRichTextProps>(), {
    modelValue: '',
    label: '',
    tooltip: '',
    id: '',
    name: '',
    required: false,
    minLength: 0,
    maxLength: 0,
    helper: '',
    placeholder: '',
    readonly: false,
    externalError: '',
    showSuccessState: true,
    toolbar: 'essential' as MdToolbarPreset,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'blur'): void;
}>();

const editorRef = ref<InstanceType<typeof QuillEditor> | null>(null);
const innerValue = ref(props.modelValue ?? '');
const internalError = ref<string>('');
const touched = ref(false);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdRichText_${props.id || instance?.uid || Math.random().toString(36)}`;

watch(
    () => props.modelValue,
    (val) => {
        if (val !== innerValue.value) {
            innerValue.value = val ?? '';
        }
    }
);

watch(innerValue, (val) => {
    emit('update:modelValue', val);
    if (touched.value) {
        validate();
    }
});

const plainText = computed(() => {
    const html = innerValue.value || '';
    return html
        .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, ' ')
        .replace(/<[^>]+>/g, ' ')
        .replace(/&nbsp;/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
});

const hasValue = computed(() => plainText.value.length > 0);

const displayedError = computed(
    () => props.externalError || internalError.value
);

const validate = () => {
    let error = '';
    const len = plainText.value.length;

    if (props.required && len === 0) {
        error = 'Este campo es obligatorio.';
    } else if (props.minLength && len > 0 && len < props.minLength) {
        error = `Mínimo ${props.minLength} caracteres.`;
    } else if (props.maxLength && len > props.maxLength) {
        error = `Máximo ${props.maxLength} caracteres.`;
    }

    internalError.value = error;
    return !error;
};

const handleBlur = () => {
    touched.value = true;
    validate();
    emit('blur');
};

const focus = () => {
    const q = (editorRef.value as any)?.getQuill?.();
    if (q && typeof q.focus === 'function') {
        q.focus();
    }
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
.md-rich-text {
    width: 100%;
}

.quill-comentarios .ql-toolbar.ql-snow {
    border: 1px solid #d1d5db;
    border-bottom: none;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    padding: 4px 8px;
}

.quill-comentarios .ql-container.ql-snow {
    border: 1px solid #d1d5db;
    border-top: none;
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.quill-comentarios .ql-editor {
    min-height: 120px;
    overflow-wrap: break-word;
    word-break: break-word;
    padding: 8px 12px;
    font-size: 0.95rem;
}

.md-error .ql-toolbar.ql-snow,
.md-error .ql-container.ql-snow {
    border-color: #ef4444;
}

.md-success .ql-toolbar.ql-snow,
.md-success .ql-container.ql-snow {
    border-color: #16a34a;
}
</style>
