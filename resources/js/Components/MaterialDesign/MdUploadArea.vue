<!-- resources/js/Components/MaterialDesign/MdUploadArea.vue -->
<template>
    <div class="w-full">
        <label v-if="label" class="block mb-1 text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-600">*</span>
        </label>

        <div
            class="flex flex-col items-center justify-center px-4 py-6 transition border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-neutral-900"
            :class="dropzoneClasses"
            @click="triggerSelect"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
        >
            <div class="flex flex-col items-center gap-2">
                <v-icon
                    v-if="icon"
                    :icon="icon"
                    size="32"
                    class="mb-1"
                />

                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                    Arrastra y suelta archivos aqui
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    O haz clic para seleccionarlos
                </p>

                <p v-if="acceptText" class="mt-1 text-xs text-gray-400">
                    {{ acceptText }}
                </p>

                <p v-if="maxSizeMB" class="mt-1 text-xs text-gray-400">
                    Tamaño maximo por archivo: {{ maxSizeMB }} MB
                </p>

                <p v-if="maxFiles" class="text-xs text-gray-400">
                    Maximo de archivos: {{ maxFiles }}
                </p>
            </div>
        </div>

        <input
            ref="fileInputRef"
            type="file"
            class="hidden"
            :multiple="multiple"
            :accept="accept"
            @change="onFileChange"
        />

        <!-- Preview lista archivos -->
        <div v-if="filesArray.length" class="mt-3 space-y-2">
            <div
                v-for="file in filesArray"
                :key="fileKey(file)"
                class="flex items-center justify-between px-3 py-2 text-sm bg-white border border-gray-200 rounded-md dark:border-neutral-700 dark:bg-neutral-900"
            >
                <div class="flex items-center gap-2">
                    <v-icon icon="mdi-file-outline" size="20" class="text-gray-500" />
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-800 dark:text-gray-100">
                            {{ file.name }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ formatSize(file.size) }}
                        </span>
                    </div>
                </div>

                <button
                    type="button"
                    class="text-xs text-red-600 hover:underline"
                    @click.stop="removeFile(file)"
                >
                    Quitar
                </button>
            </div>
        </div>

        <!-- Mensajes -->
        <p v-if="displayedError" class="mt-1 text-xs text-red-600">
            {{ displayedError }}
        </p>
        <p v-else-if="description" class="mt-1 text-xs text-gray-500">
            {{ description }}
        </p>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface MdUploadAreaProps {
    modelValue?: File | File[] | null;
    label?: string;
    description?: string;
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
    label: '',
    description: '',
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

const displayedError = computed(() => props.externalError || errorMessage.value);

const acceptText = computed(() => {
    if (!props.accept) return '';
    return `Formatos permitidos: ${props.accept}`;
});

const dropzoneClasses = computed(() => {
    const base: string[] = [];

    if (isDragging.value) {
        base.push('border-primary-500 bg-primary-50/60');
    } else {
        base.push('border-gray-300 hover:border-primary-400');
    }

    if (displayedError.value) {
        base.push('border-red-500 bg-red-50/60');
    } else if (props.showSuccessState && touched.value && !displayedError.value && filesArray.value.length) {
        base.push('border-green-500');
    }

    return base.join(' ');
});

// validation helpers
const isFileAllowed = (file: File): boolean => {
    if (!props.accept) return true;

    const acceptList = props.accept.split(',').map(a => a.trim().toLowerCase());
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

    if (!empty && props.maxFiles && props.maxFiles > 0 && (files as File[]).length > props.maxFiles) {
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

    const remaining = rawValue.value.filter(f => f !== file);
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

const fileKey = (file: File) => `${file.name}-${file.size}-${file.lastModified}`;

const validate = (): boolean => {
    const current = filesArray.value;
    return validateFiles(current.length ? current : null);
};

defineExpose({ validate });
</script>
