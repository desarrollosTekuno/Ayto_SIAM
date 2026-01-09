<!-- resources/js/Components/MaterialDesign/MdModal.vue -->
<template>
    <v-dialog
        v-model="internalValue"
        :max-width="maxWidth"
        :persistent="persistent"
        :scrollable="false"
        :position="position === 'top' ? 'top' : undefined"
        v-bind="$attrs"
    >
        <v-card :rounded="rounded" class="flex flex-col max-h-[80vh]">

                <header>
                    <slot name="header" :close="close">
                        <v-toolbar
                            :color="headerColor"
                            :flat="!headerElevated"
                            density="comfortable"
                        >
                            <v-toolbar-title class="text-subtitle-1 font-weight-medium">
                                {{ title }}
                            </v-toolbar-title>

                            <v-spacer />

                            <v-btn v-if="closable" icon variant="text" @click="close">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-toolbar>
                    </slot>
                </header>

            <section class="flex-1 overflow-y-auto mx-3 py-2 my-2">
                <v-card-text :class="[bodyClass]" style="padding: 1px;">
                    <template v-if="props.loading">
                        <v-skeleton-loader
                            type="article, actions"
                            class="p-4"
                        />
                    </template>
                    <template v-else>
                        <slot name="content"></slot>
                    </template>
                </v-card-text>
            </section>

            <!-- FOOTER-->
            <footer v-if="showFooter" class="bg-white shrink-0 dark:bg-gray-900">
                <v-divider />
                <v-card-actions class="flex justify-end mx-2">
                    <slot name="footer" :cancel="onCancel" :confirm="onConfirm" :close="close" >
                        <v-btn v-if="showCancel" variant="text" @click="onCancel">
                            {{ cancelText }}
                        </v-btn>
                        <v-btn v-if="showConfirm" :color="confirmColor" @click="onConfirm">
                            {{ confirmText }}
                        </v-btn>
                    </slot>
                </v-card-actions>
            </footer>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
    modelValue: boolean
    title?: string
    maxWidth?: number | string
    persistent?: boolean
    scrollable?: boolean
    headerColor?: string
    headerElevated?: boolean
    showFooter?: boolean
    showCancel?: boolean
    showConfirm?: boolean
    cancelText?: string
    confirmText?: string
    confirmColor?: string
    closable?: boolean
    rounded?: string | number | boolean
    bodyClass?: string
    position?: 'center' | 'top'
    loading?: boolean
}>(), {
    title: '',
    maxWidth: 600,
    persistent: false,
    scrollable: true,

    headerColor: '',
    headerElevated: false,

    showFooter: true,
    showCancel: true,
    showConfirm: true,
    cancelText: 'Cancelar',
    confirmText: 'Guardar',
    confirmColor: 'primary',
    closable: true,
    rounded: 'lg',
    bodyClass: '',
    position: 'top',
    loading: false,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'open'): void
  (e: 'close'): void
  (e: 'cancel'): void
  (e: 'confirm'): void
}>()

const internalValue = computed<boolean>({
  get: () => props.modelValue,
  set: (value) => {
    if (!value) emit('close')
    else emit('open')
    emit('update:modelValue', value)
  },
})

function close() {
  internalValue.value = false
}

function onCancel() {
  emit('cancel')
  close()
}

function onConfirm() {
  emit('confirm')
}
</script>
