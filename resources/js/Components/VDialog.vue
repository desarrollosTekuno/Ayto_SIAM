<!-- MdModal.vue -->
<template>
  <v-dialog
    v-model="internalValue"
    :max-width="maxWidth"
    :persistent="persistent"
    :scrollable="scrollable"
    v-bind="$attrs"
  >
    <v-card :rounded="rounded">
      <!-- HEADER -->
      <header>
        <slot
          name="header"
          :close="close"
        >
          <!-- Header por defecto -->
          <v-toolbar
            :color="headerColor"
            :flat="!headerElevated"
            density="comfortable"
          >
            <v-toolbar-title class="text-subtitle-1 font-weight-medium">
              {{ title }}
            </v-toolbar-title>

            <v-spacer />

            <v-btn
              v-if="closable"
              icon
              variant="text"
              @click="close"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>
        </slot>
      </header>

      <!-- CONTENT -->
      <section>
        <v-card-text :class="bodyClass">
          <slot />
        </v-card-text>
      </section>

      <!-- FOOTER -->
      <footer v-if="showFooter">
        <v-divider />
        <v-card-actions class="px-4 py-3">
          <slot
            name="footer"
            :cancel="onCancel"
            :confirm="onConfirm"
            :close="close"
          >
            <!-- Footer por defecto -->
            <v-spacer />
            <v-btn
              v-if="showCancel"
              variant="text"
              @click="onCancel"
            >
              {{ cancelText }}
            </v-btn>
            <v-btn
              v-if="showConfirm"
              :color="confirmColor"
              @click="onConfirm"
            >
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
