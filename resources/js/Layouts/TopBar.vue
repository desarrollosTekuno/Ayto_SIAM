<!-- TopBar.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
    title: { type: String, default: 'INICIO' },
    soporteUrl: {
        type: String,
        default: "https://soporte.mizzadinamicos.mx/soporte-mizza",
    },
})

const emit = defineEmits(['toggle-drawer'])

type AuthUser = { name: string; email?: string }
type PageProps = { auth?: { user?: AuthUser } }

const page = usePage<PageProps>()

const IrASoporte = () => {
    window.open(props.soporteUrl, "_blank", "noopener,noreferrer")
}

const userName = computed(() => page.props.auth?.user?.name ?? 'Usuario')

const initials = computed(() => {
    const parts = userName.value.trim().split(/\s+/).filter(Boolean)
    const a = parts[0]?.[0] ?? 'U'
    const b = parts.length > 1 ? (parts[parts.length - 1]?.[0] ?? '') : ''
    return (a + b).toUpperCase()
})
</script>

<template>
    <v-app-bar app fixed height="52" elevation="4" class="text-white border-b !bg-app-secondary border-black/10">
        <!-- Toggle -->
        <v-btn icon variant="text" class="text-white/90 hover:text-white" @click="emit('toggle-drawer')">
            <v-icon>mdi-menu</v-icon>
        </v-btn>

        <v-toolbar-title class="ml-1 font-semibold tracking-wide">
            <span class="opacity-95">{{ props.title }}</span>
        </v-toolbar-title>

        <v-spacer />

        <slot name="actions">
            <v-btn variant="text" class="text-white/90 hover:text-white" @click="IrASoporte">
                <v-icon class="mr-2">mdi-headset</v-icon>
                <span class="hidden sm:inline">Soporte</span>
            </v-btn>

        <v-btn icon variant="text" class="text-white/90 hover:text-white">
            <v-icon>mdi-bell-outline</v-icon>
        </v-btn>

        <v-divider vertical class="mx-2 opacity-25" />

        <!-- Usuario -->
        <v-menu location="bottom end" transition="fade-transition">
            <template #activator="{ props: act }">
            <v-btn v-bind="act" variant="text" class="px-2 text-white hover:text-white">
                <span class="hidden mr-2 font-semibold sm:inline opacity-90">
                {{ userName }}
                </span>

                <v-avatar
                size="32"
                class="shadow-md border border-white/20 !bg-app-primary text-white"
                >
                <span class="font-bold text-[12px]">{{ initials }}</span>
                </v-avatar>

                <v-icon class="ml-1 opacity-80">mdi-chevron-down</v-icon>
            </v-btn>
            </template>

            <v-card class="min-w-[220px]">
                <v-list density="compact">
                    <Link :href="route('logout')" method="post" as="button" class="w-full text-left">
                    <v-list-item title="Salir" prepend-icon="mdi-logout" />
                    </Link>
                </v-list>
            </v-card>
        </v-menu>
        </slot>
    </v-app-bar>
</template>
