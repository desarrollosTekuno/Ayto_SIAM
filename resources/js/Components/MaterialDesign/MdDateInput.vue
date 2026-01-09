<!-- resources/js/Components/MaterialDesign/MdDateInput.vue -->
<template>
    <div class="w-full h-18 py-2 px-2">
        <v-text-field
            ref="inputRef"
            v-model="innerValueString"
            :id="id || name || undefined"
            :type="inputType"
            :min="computedMin"
            :max="computedMax"
            :variant="variant"
            :density="density"
            :rounded="rounded"
            hide-details="auto"
            :clearable="clearable && !readonly"
            :hint="computedHint"
            :persistent-hint="persistentHint"
            :readonly="readonly"
            :error="!!displayedError"
            :error-messages="displayedError"
            :class="[successClass, readonlyClass]"
            :prepend-inner-icon="icon"
            prepend-icon=""
            @blur="handleBlur"
            @click:clear="handleClear"
        >
            <template #label>
                <span class="inline-flex items-center gap-1">
                    <span v-if="required" class="text-red-500 font-bold">*</span>
                    <span>{{ label }}</span>

                    <v-tooltip v-if="tooltip" location="top">
                        <template #activator="{ props }">
                            <v-icon v-bind="props" size="16" class="text-gray-400 cursor-help">
                                mdi-information-outline
                            </v-icon>
                        </template>
                        <span>{{ tooltip }}</span>
                    </v-tooltip>
                </span>
            </template>
        </v-text-field>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted, onBeforeUnmount, getCurrentInstance } from 'vue'
import { useMdForm } from '@/utils/MdFormContext'

type Density = 'default' | 'comfortable' | 'compact'
type Variant =
    | 'outlined'
    | 'filled'
    | 'plain'
    | 'solo'
    | 'solo-filled'
    | 'solo-inverted'
    | 'underlined'

type SingleDate = string | Date | null
type MultiDate = (string | Date)[]
type ModelValue = SingleDate | MultiDate

interface MdDateInputProps {
    modelValue?: ModelValue
    id?: string
    name?: string
    label?: string
    tooltip?: string
    icon?: string
    required?: boolean
    helper?: string
    helperPersistent?: boolean
    readonly?: boolean
    externalError?: string
    showSuccessState?: boolean
    density?: Density
    variant?: Variant
    rounded?: boolean | string | number
    clearable?: boolean
    min?: string | null
    max?: string | null
    multiple?: boolean
    range?: boolean
    locale?: string
    mode?: 'date' | 'datetime-local'
}

const props = withDefaults(defineProps<MdDateInputProps>(), {
    id: undefined,
    name: undefined,
    modelValue: null,
    label: '',
    tooltip: '',
    icon: 'mdi-calendar',
    required: false,
    helper: '',
    helperPersistent: false,
    readonly: false,
    externalError: '',
    showSuccessState: true,
    density: 'compact',
    variant: 'outlined',
    rounded: 'lg',
    clearable: true,
    min: null,
    max: null,
    multiple: false,
    range: false,
    locale: 'es-MX',
    mode: 'date',
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: ModelValue): void
}>()

const rawValue = ref<ModelValue>(props.modelValue ?? null)
const errorMessage = ref<string>('')
const touched = ref(false)

const inputRef = ref<any | null>(null)

const mdForm = useMdForm()
const instance = getCurrentInstance()
const fieldKey = props.name || `MdDateInput_${instance?.uid ?? Math.random().toString(36)}`

watch(
    () => props.modelValue,
    (val) => {
        rawValue.value = val ?? null
    },
    { immediate: true }
)

const displayedError = computed<string>(() => props.externalError || errorMessage.value)

const successClass = computed<string>(() => {
    if (!props.showSuccessState) return ''
    if (!touched.value) return ''
    if (displayedError.value) return ''
    return 'md-input-success'
})

const readonlyClass = computed<string>(() => (props.readonly ? 'md-input-readonly' : ''))

/** Helper: no lo mostramos si hay error; si es readonly, lo dejamos explícito */
const computedHint = computed<string>(() => {
    if (displayedError.value) return ''
    if (props.readonly) return 'Solo lectura'
    return (props.helper ?? '').trim()
})

/** Controla si el helper es persistente o solo al focus (readonly siempre persistente si hay hint) */
const persistentHint = computed<boolean>(() => {
    return !!computedHint.value && (!!props.helperPersistent || props.readonly)
})

const inputType = computed(() => props.mode)

/**
 * HTML date espera strings:
 * - date: "YYYY-MM-DD"
 * - datetime-local: "YYYY-MM-DDTHH:mm"
 */
const toInputString = (v: ModelValue): string => {
    if (!v || Array.isArray(v)) return ''

    if (typeof v === 'string') return v

    const d = v as Date
    if (Number.isNaN(d.getTime())) return ''

    const pad = (n: number) => String(n).padStart(2, '0')
    const yyyy = d.getFullYear()
    const mm = pad(d.getMonth() + 1)
    const dd = pad(d.getDate())

    if (props.mode === 'datetime-local') {
        const hh = pad(d.getHours())
        const mi = pad(d.getMinutes())
        return `${yyyy}-${mm}-${dd}T${hh}:${mi}`
    }

    return `${yyyy}-${mm}-${dd}`
}

const innerValueString = computed<string>({
    get() {
        return toInputString(rawValue.value)
    },
    set(value) {
        rawValue.value = value || null
        emit('update:modelValue', rawValue.value)
    },
})

const computedMin = computed(() => props.min ?? undefined)
const computedMax = computed(() => props.max ?? undefined)

const handleClear = () => {
    if (props.readonly) return
    rawValue.value = null
    emit('update:modelValue', null)
}

const validate = (): boolean => {
    if (props.readonly) {
        errorMessage.value = ''
        return true
    }

    touched.value = true

    if (props.multiple || props.range) {
        errorMessage.value = 'Este campo no soporta multiple/range en modo estable'
        return false
    }

    const v = rawValue.value as SingleDate

    if (props.required) {
        if (!v || String(v).trim() === '') {
            errorMessage.value = 'Este campo es obligatorio'
            return false
        }
    }

    if (typeof v === 'string' && v) {
        if (props.min && v < props.min) {
            errorMessage.value = `La fecha debe ser >= ${props.min}`
            return false
        }
        if (props.max && v > props.max) {
            errorMessage.value = `La fecha debe ser <= ${props.max}`
            return false
        }
    }

    errorMessage.value = ''
    return true
}

const handleBlur = () => {
    validate()
}

const focus = () => {
    if (props.readonly) return

    const comp = inputRef.value as any
    if (!comp) return

    if (typeof comp.focus === 'function') {
        comp.focus()
        return
    }

    const el: HTMLInputElement | null =
        (comp.$el && comp.$el.querySelector && comp.$el.querySelector('input')) || null
    el?.focus()
}

onMounted(() => {
    mdForm?.registerField(fieldKey, { validate, focus })
})

onBeforeUnmount(() => {
    mdForm?.unregisterField(fieldKey)
})

defineExpose({ validate, focus })
</script>

<style scoped>
/* SUCCESS */
.md-input-success :deep(.v-field__outline__start),
.md-input-success :deep(.v-field__outline__notch),
.md-input-success :deep(.v-field__outline__end) {
    border-color: #16a34a !important;
}

.md-input-success :deep(.v-label) {
    color: #16a34a !important;
}

.md-input-success :deep(.v-field__prepend-inner .v-icon) {
    color: #16a34a !important;
}

/* READONLY */
.md-input-readonly :deep(.v-field) {
    background: rgba(148, 163, 184, 0.18) !important;
}

.md-input-readonly :deep(.v-field__outline__start),
.md-input-readonly :deep(.v-field__outline__notch),
.md-input-readonly :deep(.v-field__outline__end) {
    border-style: dashed !important;
    border-color: rgba(100, 116, 139, 0.75) !important;
}

.md-input-readonly :deep(input) {
    cursor: not-allowed !important;
    color: rgba(15, 23, 42, 0.75) !important;
}

.md-input-readonly :deep(.v-label) {
    color: rgba(100, 116, 139, 0.9) !important;
}

.md-input-readonly :deep(.v-field__prepend-inner .v-icon) {
    color: rgba(100, 116, 139, 0.9) !important;
}
</style>
