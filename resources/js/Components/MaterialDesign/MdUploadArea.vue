<!-- resources/js/Components/MaterialDesign/MdUploadArea.vue -->
<template>
    <div class="w-full">
        <label
            v-if="label"
            class="block mb-1 text-sm font-medium text-gray-800"
            :for="id || name"
        >
            <span class="inline-flex items-center gap-1">
                <span v-if="required" class="text-red-600 font-bold">*</span>
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
            class="flex flex-col items-center justify-center px-6 py-6 transition duration-150 ease-out bg-white border-2 border-dashed shadow-sm cursor-pointer rounded-2xl"
            :class="dropzoneClasses"
            @click="triggerSelect"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
        >
            <div class="flex flex-col items-center max-w-md gap-2 text-center">
                <div class="flex items-center justify-center w-12 h-12 mb-1 bg-gray-100 rounded-full">
                    <v-icon
                        v-if="icon"
                        :icon="icon"
                        size="26"
                        class="text-gray-500"
                    />
                </div>

                <p class="text-sm font-semibold text-gray-800">
                    Arrastra y suelta archivos aqui
                </p>
                <p class="text-xs text-gray-500">
                    O haz clic para seleccionarlos
                </p>

                <div class="mt-2 space-y-1 text-[11px] leading-snug text-gray-500">
                    <p v-if="acceptText">
                        {{ acceptText }}
                    </p>

                    <p v-if="maxSizeMB">
                        Tamaño maximo por archivo: {{ maxSizeMB }} MB
                    </p>

                    <p v-if="maxFiles">
                        Maximo de archivos: {{ maxFiles }}
                    </p>
                </div>
            </div>
        </div>

        <input
            ref="fileInputRef"
            type="file"
            class="hidden"
            :multiple="multiple"
            :accept="accept"
            :name="name"
            :id="id || name"
            @change="onFileChange"
        />

        <div v-if="filesArray.length" class="mt-3 space-y-2">
            <div
                v-for="file in filesArray"
                :key="fileKey(file)"
                class="flex items-center justify-between px-3 py-2 text-sm bg-white border border-gray-200 rounded-xl"
            >
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center bg-gray-100 rounded-full w-7 h-7">
                        <v-icon icon="mdi-file-outline" size="18" class="text-gray-500" />
                    </div>
                    <div class="flex flex-col">
                        <span class="max-w-xs font-medium text-gray-800 truncate">
                            {{ file.name }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ formatSize(file.size) }}
                        </span>
                    </div>
                </div>

                <button
                    type="button"
                    class="text-xs font-medium text-red-600 hover:text-red-700 hover:underline"
                    @click.stop="removeFile(file)"
                >
                    Quitar
                </button>
            </div>
        </div>

        <p v-if="displayedError" class="mt-1 text-xs text-red-600">
            {{ displayedError }}
        </p>
        <p v-else-if="computedHint" class="mt-1 text-xs text-gray-500">
            {{ computedHint }}
        </p>
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

interface MdUploadAreaProps {
    modelValue?: File | File[] | null;
    id?: string;
    name?: string;
    label?: string;
    tooltip?: string;
    description?: string;
    helperPersistent?: boolean;
    required?: boolean;
    multiple?: boolean;
    accept?: string;
    externalError?: string;
    showSuccessState?: boolean;
    maxSizeMB?: number | null;
    maxFiles?: number | null;
    icon?: string;
}

const props = withDefaults(defineProps<MdUploadAreaProps>(), {
    modelValue: null,
    id: undefined,
    name: undefined,
    label: '',
    tooltip: '',
    description: '',
    helperPersistent: false,
    required: false,
    multiple: false,
    accept: undefined,
    externalError: '',
    showSuccessState: true,
    maxSizeMB: null,
    maxFiles: null,
    icon: 'mdi-cloud-upload-outline',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | File[] | null): void;
}>();

const rawValue = ref<File | File[] | null>(props.modelValue ?? null);
const touched = ref(false);
const errorMessage = ref('');
const isDragging = ref(false);
const fileInputRef = ref<HTMLInputElement | null>(null);

const mdForm = useMdForm();
const instance = getCurrentInstance();
const fieldKey =
    props.name ||
    `MdUploadArea_${props.id || instance?.uid || Math.random().toString(36)}`;

watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val ?? null;
    },
    { immediate: true }
);

const innerValue = computed({
    get: () => rawValue.value,
    set: (v: File | File[] | null) => {
        rawValue.value = v;
        emit('update:modelValue', v);
    },
});

const filesArray = computed<File[]>(() => {
    if (!rawValue.value) return [];
    return Array.isArray(rawValue.value) ? rawValue.value : [rawValue.value];
});

const displayedError = computed(
    () => props.externalError || errorMessage.value
);

const acceptText = computed(() => {
    if (!props.accept) return '';
    return `Formatos permitidos: ${props.accept}`;
});

const computedHint = computed(() => {
    if (displayedError.value) return '';
    const txt = (props.description ?? '').trim();
    if (!txt) return '';
    if (props.helperPersistent) return txt;
    return touched.value ? txt : '';
});

const dropzoneClasses = computed(() => {
    const base: string[] = [];

    if (isDragging.value) {
        base.push('border-blue-400 bg-blue-50');
    } else {
        base.push('border-gray-300 hover:border-blue-400 hover:bg-gray-50');
    }

    if (displayedError.value) {
        base.push('border-red-500 bg-red-50');
    } else if (
        props.showSuccessState &&
        touched.value &&
        !displayedError.value &&
        filesArray.value.length
    ) {
        base.push('border-emerald-500 bg-emerald-50');
    }

    return base.join(' ');
});

const isFileAllowed = (file: File): boolean => {
    if (!props.accept) return true;

    const acceptList = props.accept.split(',').map((a) => a.trim().toLowerCase());
    const fileName = file.name.toLowerCase();
    const fileType = (file.type || '').toLowerCase();

    for (const rule of acceptList) {
        if (!rule) continue;

        if (rule.startsWith('.')) {
            if (fileName.endsWith(rule)) return true;
        } else if (rule.includes('/')) {
            const [type, subtype] = rule.split('/');
            const [fType, fSubtype] = fileType.split('/');

            if (!fType) continue;

            if (type === fType && (subtype === '*' || subtype === fSubtype)) {
                return true;
            }
        }
    }

    return false;
};

const validateFiles = (files: File[] | null): boolean => {
    touched.value = true;

    const empty = !files || files.length === 0;

    if (props.required && empty) {
        errorMessage.value = 'Este campo es obligatorio';
        return false;
    }

    if (!empty && props.accept) {
        for (const f of files as File[]) {
            if (!isFileAllowed(f)) {
                errorMessage.value = `El archivo ${f.name} no esta permitido`;
                return false;
            }
        }
    }

    if (!empty && props.maxSizeMB && props.maxSizeMB > 0) {
        const maxBytes = props.maxSizeMB * 1024 * 1024;
        for (const f of files as File[]) {
            if (f.size > maxBytes) {
                errorMessage.value = `El archivo ${f.name} excede ${props.maxSizeMB} MB`;
                return false;
            }
        }
    }

    if (
        !empty &&
        props.maxFiles &&
        props.maxFiles > 0 &&
        (files as File[]).length > props.maxFiles
    ) {
        errorMessage.value = `Solo se permiten hasta ${props.maxFiles} archivos`;
        return false;
    }

    errorMessage.value = '';
    return true;
};

const applyFiles = (files: FileList | File[]) => {
    const arr = Array.from(files);

    const valid = validateFiles(arr);
    if (!valid) return;

    if (props.multiple) {
        innerValue.value = arr;
    } else {
        innerValue.value = arr[0] ?? null;
    }
};

const triggerSelect = () => {
    fileInputRef.value?.click();
};

const onFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (!input.files) return;
    applyFiles(input.files);
    input.value = '';
};

const onDragOver = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (!event.dataTransfer?.files?.length) return;
    applyFiles(event.dataTransfer.files);
};

const removeFile = (file: File) => {
    if (!rawValue.value) return;

    if (!Array.isArray(rawValue.value)) {
        innerValue.value = null;
        validateFiles(null);
        return;
    }

    const remaining = rawValue.value.filter((f) => f !== file);
    innerValue.value = remaining.length ? remaining : null;
    validateFiles(remaining.length ? remaining : null);
};

const formatSize = (bytes: number): string => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const value = bytes / Math.pow(k, i);
    return `${value.toFixed(1)} ${sizes[i]}`;
};

const fileKey = (file: File) =>
    `${file.name}-${file.size}-${file.lastModified}`;

const validate = (): boolean => {
    const current = filesArray.value;
    return validateFiles(current.length ? current : null);
};

const focus = () => {
    fileInputRef.value?.focus();
};

onMounted(() => {
    mdForm?.registerField(fieldKey, { validate, focus });
});

onBeforeUnmount(() => {
    mdForm?.unregisterField(fieldKey);
});

defineExpose({ validate, focus });
</script>
