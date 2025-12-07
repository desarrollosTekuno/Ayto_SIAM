<template>
  <v-stepper
    v-model="internalValue"
    :mobile="mobile"
    :vertical="vertical"
    v-bind="$attrs"
  >
    <template #default="{ prev, next }">
      <slot
        :prev="prev"
        :next="() => handleNext(next)"
      >
        <!-- HEADER AUTOMATICO -->
        <v-stepper-header v-if="items && items.length">
          <template
            v-for="(step, index) in items"
            :key="String(step.value)"
          >
            <!-- Slot opcional para personalizar cada item del header -->
            <template v-if="$slots['header-item']">
              <slot
                name="header-item"
                :item="step"
                :index="index"
              />
            </template>
            <template v-else>
              <v-stepper-item v-bind="step" />
            </template>

            <v-divider v-if="index < items.length - 1" />
          </template>
        </v-stepper-header>

        <!-- CONTENIDO (WINDOW) AUTOMATICO -->
        <v-stepper-window v-if="items && items.length">
          <v-stepper-window-item
            v-for="step in items"
            :key="String(step.value)"
            :value="step.value"
          >
            <!-- slot item.{value} tiene prioridad -->
            <slot
              :name="`item.${step.value}`"
              :step="step"
            >
              <!-- luego slot item generico -->
              <slot
                name="item"
                :step="step"
              >
                <!-- fallback por defecto -->
                <div class="pa-4">
                  <h3 class="mb-2 text-h6">
                    {{ step.title }}
                  </h3>
                  <p v-if="step.subtitle" class="text-body-2">
                    {{ step.subtitle }}
                  </p>
                </div>
              </slot>
            </slot>
          </v-stepper-window-item>
        </v-stepper-window>

        <!-- ACCIONES -->
        <!-- 1) Acciones automáticas SIN slot personalizado -->
        <v-stepper-actions
          v-if="!hideActions && items && items.length && !$slots.actions"
          :prev-text="prevText"
          :next-text="isLastStep ? finishText : nextText"
          @click:prev="prev"
          @click:next="handleNext(next)"
        />

        <!-- 2) Acciones PERSONALIZADAS cuando se usa #actions -->
        <div
          v-if="!hideActions && items && items.length && $slots.actions"
          class="px-4 pt-2 pb-4 d-flex align-center justify-space-between"
        >
          <slot
            name="actions"
            :prev="prev"
            :next="() => handleNext(next)"
            :is-last="isLastStep"
          />
        </div>
      </slot>
    </template>
  </v-stepper>
</template>

<script setup lang="ts">
import { computed } from 'vue'

type StepValue = string | number

export interface MdStepItem {
  value: StepValue
  title?: string
  subtitle?: string
  icon?: string
  color?: string
  editable?: boolean
  disabled?: boolean
  complete?: boolean
  error?: boolean
  [key: string]: unknown
}

const props = withDefaults(defineProps<{
  modelValue: StepValue
  items?: MdStepItem[]
  mobile?: boolean
  vertical?: boolean
  hideActions?: boolean
  prevText?: string
  nextText?: string
  finishText?: string
}>(), {
  items: () => [],
  mobile: false,
  vertical: false,
  hideActions: false,
  prevText: 'Anterior',
  nextText: 'Siguiente',
  finishText: 'Finalizar',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: StepValue): void
  (e: 'finish'): void
}>()

const internalValue = computed<StepValue>({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const isLastStep = computed(() => {
  if (!props.items?.length) return false
  const index = props.items.findIndex(
    (item) => item.value === props.modelValue,
  )
  return index !== -1 && index === props.items.length - 1
})

function handleNext(next: () => void) {
  if (isLastStep.value) {
    emit('finish')
    return
  }

  next()
}
</script>
