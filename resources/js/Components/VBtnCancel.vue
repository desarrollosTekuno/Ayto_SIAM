<template>
  <VButton
    v-bind="$attrs"
    :color="color"
    :variant="variant"
    :rounded="rounded"
    :type="type"
    :loading="loading"
    :disabled="disabled"
    class="siam-btn-cancel"
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
    color: 'primary',
    variant: 'outlined',
    rounded: 'lg',
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
.siam-btn-cancel {
  font-weight: 500;
  text-transform: none;
}
</style>
