<!-- resources/js/Components/FormValidate.vue -->
<template>
    <form @submit.prevent="onSubmit" class="contents">
        <slot
            :validateAll="validateAll"
            :isSubmitting="isSubmitting"
        />
    </form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useProvideMdForm } from '@/utils/MdFormContext';

const emit = defineEmits<{
    (e: 'submit'): void;
    (e: 'invalid'): void;
}>();

const { validateAll, getFields } = useProvideMdForm();
const isSubmitting = ref(false);

const onSubmit = () => {
    if (isSubmitting.value) return;

    isSubmitting.value = true;

    const ok = validateAll();

    if (!ok) {
        emit('invalid');
        focusFirstInvalid();
        isSubmitting.value = false;
        return;
    }

    emit('submit');
    isSubmitting.value = false;
};

/**
 * Busca el primer campo inválido y llama focus()
 */
const focusFirstInvalid = () => {
    const fields = getFields();

    for (const [key, field] of Object.entries(fields)) {
        const valid = field.validate();
        if (!valid) {
            if (typeof field.focus === 'function') {
                field.focus();
            }
            break;
        }
    }
};


defineExpose({
    submit: onSubmit,
    validateAll,
    focusFirstInvalid,
});
</script>
