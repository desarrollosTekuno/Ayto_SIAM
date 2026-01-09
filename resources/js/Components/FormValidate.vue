<!-- resources/js/Components/FormValidate.vue -->
<template>
    <form @submit.prevent="onSubmit" class="contents">
        <slot
            :validateAll="validateAll"
            :isSubmitting="isSubmitting"
            :isValid="isValid"
            :isTouched="isTouched"
        />
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useProvideMdForm } from '@/utils/MdFormContext'

const emit = defineEmits<{
    (e: 'submit'): void
    (e: 'invalid'): void
}>()

const { validateAll, getFields, isValid, isTouched } = useProvideMdForm()
const isSubmitting = ref(false)

const onSubmit = () => {
    if (isSubmitting.value) return

    isSubmitting.value = true

    const ok = validateAll()

    if (!ok) {
        emit('invalid')
        focusFirstInvalid()
        isSubmitting.value = false
        return
    }

    emit('submit')
    isSubmitting.value = false
}

const focusFirstInvalid = () => {
    const fields = getFields()

    for (const field of Object.values(fields)) {
        const valid = field.validate()
        if (!valid) {
            if (typeof field.focus === 'function') field.focus()
            break
        }
    }
}

defineExpose({
    submit: onSubmit,
    validateAll,
    focusFirstInvalid,
    isValid,
    isTouched,
    isSubmitting,
})
</script>
