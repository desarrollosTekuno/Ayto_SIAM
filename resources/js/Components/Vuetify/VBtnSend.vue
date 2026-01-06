<!-- resources/js/Components/Vuetify/VBtnSend.vue -->
<template>
    <VButton
        v-bind="$attrs"
        :color="color"
        :variant="variant"
        :rounded="rounded"
        :type="type"
        :loading="loading"
        :disabled="disabled"
        size="small"
        class="siam-btn-primary"
        @click="emitClick"
    >
        <slot />
    </VButton>
</template>

<script setup lang="ts">
// @ts-nocheck
import VButton from './VButton.vue'

type BtnVariant = 'flat' | 'text' | 'elevated' | 'outlined' | 'plain' | 'tonal'
type BtnType = 'button' | 'submit' | 'reset'

const props = withDefaults(defineProps<{
    color?: string
    variant?: BtnVariant
    rounded?: string | number
    type?: BtnType
    loading?: boolean
    disabled?: boolean
    }>(), {
    color: '#0f766e',
    variant: 'elevated',
    rounded: 'button',
    type: 'submit',
    loading: false,
    disabled: false,
})

const emit = defineEmits<{
    (e: 'click', ev: MouseEvent): void
}>()

const emitClick = (ev: MouseEvent) => {
  if (!props.loading && !props.disabled) emit('click', ev)
}
</script>

<style scoped>
.siam-btn-primary {
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
}
</style>
