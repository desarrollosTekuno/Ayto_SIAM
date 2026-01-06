<!-- resources/js/Components/Vuetify/DataTableServer.vue -->
<template>
    <v-data-table-server
        class="vuetify-data-table"
        v-bind="attrs"
        v-model:options="internalOptions"
        :headers="vuetifyHeaders"
        :items="items"
        :items-length="itemsLength"
        :loading="loading"
        :items-per-page="itemsPerPage"
        :items-per-page-options="itemsPerPageOptions"
        :items-per-page-text="'Paginación:'"
        :density="density"
        :item-value="itemValue"
        :fixed-header="fixedHeader"
        :fixed-footer="fixedFooter"
        :height="height"
        @update:options="onUpdateOptions"
    >
        <!-- HEADER -->
        <template #top="slotProps">
            <slot name="top" v-bind="slotProps">
                <div v-if="showHeader" class="px-2 py-2 vuetify-dt-header">
                    <!-- Buscador -->
                    <div v-if="searchable" class="vuetify-dt-search-wrapper">
                        <v-text-field
                            v-model="localSearch"
                            :label="searchLabel"
                            :placeholder="searchPlaceholder"
                            density="compact"
                            variant="solo"
                            prepend-inner-icon="mdi-magnify"
                            hide-details
                            clearable
                            class="vuetify-dt-search ma-0 pa-0"
                        />
                    </div>
                </div>
            </slot>
        </template>

        <template
            v-for="name in bodySlotNames"
            :key="name"
            #[name]="slotProps"
        >
            <slot :name="name" v-bind="slotProps" />
        </template>
    </v-data-table-server>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useAttrs, useSlots } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    columns: {
        type: Array,
        default: () => [],
    },
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
    itemsPerPage: {
        type: Number,
        default: 10,
    },
    itemsPerPageOptions: {
        type: Array,
        default: () => [25, 50, 75, 100],
    },

    loading: {
        type: Boolean,
        default: false,
    },

    options: {
        type: Object,
        default: () => ({
            page: 1,
            itemsPerPage: 10,
            sortBy: [],
            search: '',
        }),
    },

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
    title: {
        type: String,
        default: '',
    },
    showHeader: {
        type: Boolean,
        default: true,
    },
    searchable: {
        type: Boolean,
        default: false,
    },
    searchLabel: {
        type: String,
        default: 'Buscar',
    },
    searchPlaceholder: {
        type: String,
        default: 'Buscar…',
    },
    searchDebounce: {
        type: Number,
        default: 400,
    },
    density: {
        type: String,
        default: 'compact',
    },
    itemValue: {
        type: [String, Function],
        default: 'id',
    },
    fixedHeader: {
        type: Boolean,
        default: true,
    },
    fixedFooter: {
        type: Boolean,
        default: true,
    },
    height: {
        type: [String, Number],
        default: '65vh',
    },
})

const emit  = defineEmits(['update:options'])
const attrs = useAttrs()
const slots = useSlots()

const bodySlotNames = computed(() =>
    Object.keys(slots).filter((name) => name !== 'top')
)

const internalOptions = ref({ ...props.options })

const serverItems      = ref([])
const serverItemsTotal = ref(0)
const serverLoading    = ref(false)

const localSearch = ref(internalOptions.value.search ?? '')

let searchTimeout = null

watch(
    () => props.options,
    (val) => {
        internalOptions.value = { ...internalOptions.value, ...val }
        if (val?.search !== undefined && val.search !== localSearch.value) {
            localSearch.value = val.search
        }
    },
    { deep: true }
)

watch(
    () => internalOptions.value.search,
    (val) => {
        if (val !== localSearch.value) {
            localSearch.value = val ?? ''
        }
    }
)

const vuetifyHeaders = computed(() => {
    if (props.columns.length) {
        return props.columns.map(col => ({
            title: col.headerName ?? col.header ?? col.field,
            key:   col.field ?? col.key,
            sortable: col.sortable !== false,
        }))
    }
    return props.headers
})

const items = computed(() => {
    return props.serverRoute ? serverItems.value : props.items
})

const itemsLength = computed(() => {
    return props.serverRoute ? serverItemsTotal.value : props.itemsLength
})

const loading = computed(() => {
    return props.serverRoute ? serverLoading.value : props.loading
})

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
        route(props.serverRoute),
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

watch(
    () => localSearch.value,
    (val) => {
        internalOptions.value = {
            ...internalOptions.value,
            page: 1,
            search: val ?? '',
        }

        emit('update:options', internalOptions.value)

        if (props.serverRoute) {
            if (searchTimeout) clearTimeout(searchTimeout)
            searchTimeout = setTimeout(() => {
                fetchFromServer(internalOptions.value)
            }, props.searchDebounce)
        }
    }
)

function onUpdateOptions(newOptions) {
    internalOptions.value = newOptions
    emit('update:options', newOptions)

    if (props.serverRoute) {
        fetchFromServer(newOptions)
    }
}

onMounted(() => {
    if (props.serverRoute) {
        fetchFromServer(internalOptions.value)
    }
})

onBeforeUnmount(() => {
    if (searchTimeout) clearTimeout(searchTimeout)
})
</script>
