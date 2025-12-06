<template>
  <v-data-table-server
    v-model:options="internalOptions"
    :headers="vuetifyHeaders"
    :items="items"
    :items-length="itemsLength"
    :loading="loading"
    v-bind="attrs"
    @update:options="onUpdateOptions"
  >
    <!-- Reexpone TODOS los slots -->
    <template
      v-for="(_, name) in slots"
      #[name]="slotProps"
    >
      <slot :name="name" v-bind="slotProps" />
    </template>
  </v-data-table-server>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useAttrs, useSlots } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  /**
   * columns estilo AG Grid:
   * [{ headerName, field, sortable }]
   */
  columns: {
    type: Array,
    default: () => [],
  },

  /**
   * Si NO usas server-route, puedes seguir usando headers/items/itemsLength
   * como en la versión anterior (modo manual).
   */
  headers: {
    type: Array,
    default: () => [],
  },
  items: {
    type: Array,
    default: () => [],
  },
  itemsLength: {
    type: Number,
    default: 0,
  },

  loading: {
    type: Boolean,
    default: false,
  },

  // v-model:options (lo exponemos por si quieres controlarlo desde fuera)
  options: {
    type: Object,
    default: () => ({
      page: 1,
      itemsPerPage: 10,
      sortBy: [],
      search: '',
    }),
  },

  /**
   * Modo servidor "automático" (Inertia):
   * - server-route = nombre de ruta (Ziggy)
   * - server-prop  = nombre del paginator en page.props (ej: 'bancos')
   * - extra-params = filtros adicionales que quieras mandar
   */
  serverRoute: {
    type: String,
    default: null,
  },
  serverProp: {
    type: String,
    default: 'items',
  },
  extraParams: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits([
  'update:options',
])

const attrs  = useAttrs()
const slots  = useSlots()

// ------------------------------
// Estado interno
// ------------------------------
const internalOptions = ref({ ...props.options })

// Cuando usamos modo servidor automático, estos se llenan desde la respuesta
const serverItems      = ref([])
const serverItemsTotal = ref(0)
const serverLoading    = ref(false)

// Si el padre cambia options, sincronizamos
watch(
  () => props.options,
  (val) => {
    internalOptions.value = { ...internalOptions.value, ...val }
  },
  { deep: true }
)

// ------------------------------
// Headers: columns estilo AG Grid -> headers Vuetify
// ------------------------------
const vuetifyHeaders = computed(() => {
  if (props.columns.length) {
    return props.columns.map(col => ({
      title: col.headerName ?? col.header ?? col.field,
      key:   col.field ?? col.key,
      sortable: col.sortable !== false,
      // podrías mapear más props si quieres (align, width, etc.)
    }))
  }

  // fallback: usas headers directamente
  return props.headers
})

// ------------------------------
// Items / total / loading finales que usa la tabla
// ------------------------------
const items = computed(() => {
  return props.serverRoute ? serverItems.value : props.items
})

const itemsLength = computed(() => {
  return props.serverRoute ? serverItemsTotal.value : props.itemsLength
})

const loading = computed(() => {
  return props.serverRoute ? serverLoading.value : props.loading
})

// ------------------------------
// Carga desde servidor (Inertia)
// ------------------------------
async function fetchFromServer(optionsToUse) {
  if (!props.serverRoute) return

  serverLoading.value = true

  const sort = optionsToUse.sortBy?.[0] ?? null

  const query = {
    page:       optionsToUse.page,
    per_page:   optionsToUse.itemsPerPage,
    search:     optionsToUse.search ?? null,
    sort_by:    sort?.key ?? null,
    sort_dir:   sort?.order ?? null,
    ...props.extraParams,
  }

  router.get(
    route(props.serverRoute), // Ziggy
    query,
    {
      preserveState: true,
      replace: true,
      onSuccess: (page) => {
        const paginator = page.props[props.serverProp]

        if (!paginator) {
          console.warn(
            `[DataTableServer] No se encontró props.${props.serverProp} en la respuesta`
          )
          serverItems.value      = []
          serverItemsTotal.value = 0
          return
        }

        serverItems.value      = paginator.data ?? []
        serverItemsTotal.value = paginator.total ?? 0

        // Sincronizar page / per_page con lo que venga de Laravel
        internalOptions.value = {
          ...internalOptions.value,
          page:         paginator.current_page ?? internalOptions.value.page,
          itemsPerPage: paginator.per_page ?? internalOptions.value.itemsPerPage,
        }
      },
      onFinish: () => {
        serverLoading.value = false
      },
    }
  )
}

function onUpdateOptions(newOptions) {
  internalOptions.value = newOptions
  emit('update:options', newOptions)

  // Si estamos en modo servidor automático, disparar fetch
  if (props.serverRoute) {
    fetchFromServer(newOptions)
  }
}

// Carga inicial automática si serverRoute está definido
onMounted(() => {
  if (props.serverRoute) {
    fetchFromServer(internalOptions.value)
  }
})
</script>
