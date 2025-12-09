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
            <!-- HEADER -->
            <header>
                <slot name="header" :close="close">
                    <v-toolbar
                        :color="headerColor || undefined"
                        :flat="!headerElevated"
                        density="comfortable"
                        class=""
                    >
                        <v-toolbar-title
                            class="text-subtitle-1 font-weight-bold d-flex align-center"
                        >
                            <v-icon v-if="headerIcon" class="mr-2">
                                {{ headerIcon }}
                            </v-icon>
                            <span class="text-uppercase">
                                {{ title }}
                            </span>
                        </v-toolbar-title>

                        <v-spacer />

                        <v-btn v-if="closable" icon variant="text" @click="close">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>
                </slot>
            </header>

            <!-- BODY -->
            <section class="flex-1 overflow-y-auto">
                <v-card-text :class="[bodyClass]" style="padding: 0px;">
                    <template v-if="props.loading">
                        <v-skeleton-loader
                            type="article, actions"
                            class="p-4"
                        />
                    </template>
                    <template v-else>
                        <slot name="content" />
                    </template>
                </v-card-text>
            </section>

            <!-- FOOTER -->
            <footer v-if="showFooter" class="shrink-0 elevation-3">
                <v-divider />
                <v-card-actions class="flex justify-end px-4 py-3">
                    <slot
                        name="footer"
                        :cancel="onCancel"
                        :confirm="onConfirm"
                        :close="close"
                    >
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
    headerIcon?: string

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

    // Header con color por defecto (como el que estás usando)
    headerColor: '#0d9488',
    headerElevated: false,
    headerIcon: '',

    showFooter: true,
    showCancel: true,
    showConfirm: true,
    cancelText: 'Cancelar',
    confirmText: 'Guardar',
    confirmColor: 'primary',

    closable: true,
    rounded: 'sm',
    bodyClass: '',
    // POR DEFECTO ARRIBA
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
