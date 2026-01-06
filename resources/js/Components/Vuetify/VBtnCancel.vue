<!-- resources/js/Components/VBtnCancel.vue -->
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
        class="hover:shadow-lg"
        @click="emitClick"
    >
        <span class=" text-slate-500 text-uppercase font-bold hover:text-slate-700">
            <slot />
        </span>
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
    color: 'blue-grey-lighten-1',
    variant: 'tonal',
    rounded: 'sm',
    type: 'button',
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
</style>
