<!-- resources/js/Components/Vuetify/VButton.vue -->
<template>
    <v-btn
        v-bind="$attrs"
        :color="color"
        :variant="variant"
        :rounded="rounded"
        density="default"
        :type="type"
        :loading="loading"
        :disabled="disabled || loading"
        :class="computedClass"
        @click="emitClick"
    >
        <template v-if="loading">
            <span class="absolute inset-0 flex items-center justify-center">
                <svg
                    class="w-5 h-5 text-white animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"
                    />
                </svg>
            </span>
        </template>

        <template v-if="isIconBtn">
            <v-icon :icon="iconName" class="text-white" />
        </template>

        <span v-else :class="{ 'opacity-0': loading }" class="text-white">
            <slot />
        </span>
    </v-btn>
</template>

<script setup lang="ts">
import { computed, useAttrs } from 'vue'

type BtnVariant = 'flat' | 'text' | 'elevated' | 'outlined' | 'plain' | 'tonal'
type BtnType = 'button' | 'submit' | 'reset'

const attrs = useAttrs()

const props = withDefaults(defineProps<{
    color?: string
    variant?: BtnVariant
    rounded?: string | number
    type?: BtnType
    loading?: boolean
    disabled?: boolean
}>(), {
    color: 'var(--color-app-tertiary)',
    variant: 'flat',
    rounded: 'button',
    type: 'button',
    loading: false,
    disabled: false,
})

const emit = defineEmits<{
    (e: 'click', ev: MouseEvent): void
}>()

const emitClick = (e: MouseEvent) => {
    if (!props.loading && !props.disabled) emit('click', e)
}

const iconName = computed(() => {
    return typeof attrs.icon === 'string' ? (attrs.icon as string) : null
})

const isIconBtn = computed(() => !!iconName.value)

const computedClass = computed(() => [
    'relative font-semibold transition-all duration-150 text-white',
    props.disabled || props.loading ? 'cursor-not-allowed opacity-60' : '',
])
</script>

<style scoped>
.v-btn {
    text-transform: none;
    border-radius: 0.45rem;
}
</style>
