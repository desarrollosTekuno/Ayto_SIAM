<!-- resources/js/Components/Vuetify/MdTabs.vue -->
<script setup lang="ts">
import { computed } from 'vue'

type TabValue = string | number

interface TabItem {
    value: TabValue
    label: string
    icon?: string
    disabled?: boolean
}

const props = withDefaults(defineProps<{
    modelValue: TabValue
    tabs: TabItem[]
    wrap?: boolean
    }>(), {
    wrap: true,
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: TabValue): void
}>()

const selected = computed({
    get: () => props.modelValue,
    set: (v: TabValue) => emit('update:modelValue', v),
})

const selectTab = (tab: TabItem) => {
    if (tab.disabled) return
    selected.value = tab.value
}
</script>

<template>
    <div class="w-full">
        <div
        class="flex w-full border-b border-gray-200"
        :class="wrap ? 'flex-wrap gap-2' : 'flex-nowrap overflow-x-auto'"
        >
        <button
            v-for="tab in tabs"
            :key="String(tab.value)"
            type="button"
            @click="selectTab(tab)"
            :disabled="tab.disabled"
            class="relative px-4 py-2 font-semibold text-sm transition-all select-none"
            :class="[
            tab.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
            selected === tab.value
                ? 'text-app-primary bg-app-accent-1 shadow-md rounded-t-lg'
                : 'text-gray-600 hover:text-app-primary'
            ]"
        >
            <i v-if="tab.icon" class="mr-2" :class="tab.icon"></i>
            <span>{{ tab.label }}</span>

            <span
            v-if="selected === tab.value"
            class="absolute bottom-0 left-0 right-0 h-1 bg-app-accent-1 rounded-t-md"
            />
        </button>
        </div>

        <!-- Contenido controlado por el padre -->
        <div class="pt-4">
        <slot :active="selected" />
        </div>
    </div>
</template>
