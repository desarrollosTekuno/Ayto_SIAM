<script setup lang="ts">
import routes from '@/routing'
import { Link, usePage } from '@inertiajs/vue3'
import { onMounted, ref, computed } from 'vue'
import { route } from 'ziggy-js'

type AuthUser = {
    name: string
    email: string
}

type PageProps = {
    auth: {
        user: AuthUser
        permissions: string[]
    }
}

const page = usePage<PageProps>()

const can = page.props.auth.permissions
const auth = page.props.auth

const drawer = defineModel('drawer')

const props = defineProps<{ rail: boolean }>()

const opened = ref<string[]>()

const existSomeRoute = (routeNames: string | string[]): boolean => {
    return Array.isArray(routeNames)
        ? routeNames.some((r) => can.includes(r))
        : can.includes(routeNames)
}

const isActiveRoute = (name: string | string[]) => {
    return Array.isArray(name)
        ? route().current(name[0])
        : route().current(name)
}

onMounted(() => {
    opened.value =
        routes
            .filter((ruta) => ruta.groupItems?.some((gi) => route().current(gi.name)))
            .map((ruta) => ruta.group) ?? []
})
</script>

<template>
    <v-navigation-drawer
        :v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        :permanent="rail"
        :rail="$vuetify.display.mobile ? !props.rail : props.rail"
        class="font-poppins bg-customSurface"
    >
        <v-list
            class="bg-[url(/assets/images/Logo.png)]"
            open-strategy="multiple"
            :opened="opened"
            @update:opened="newOpened => { opened = newOpened; }"
            density="compact"
            nav
        >
            <div v-for="ruta in routes" :key="ruta.value">
                <div v-if="ruta.group == null">
                    <!-- <Link v-if="existSomeRoute(ruta.name)" :href="route(Array.isArray(ruta.name) ? ruta.name[0] : ruta.name)" preserve-scroll> -->
                    <Link :href="route(Array.isArray(ruta.name) ? ruta.name[0] : ruta.name)" preserve-scroll>
                        <v-list-item
                            elevation="0"
                            variant="flat"
                            density="compact"
                            :title="ruta.title"
                            :active="isActiveRoute(ruta.name)"
                            active-class="nav-item-active"
                            :class="[
                                'rounded-lg transition-all',
                                isActiveRoute(ruta.name)
                                    ? 'shadow-sm'
                                    : 'hover:bg-black/5 dark:hover:bg-white/5'
                            ]"
                        >
                            <template #prepend>
                                <v-icon :icon="ruta.icon"></v-icon>
                            </template>
                        </v-list-item>
                    </Link>
                </div>

                <div v-else>
                    <!-- <v-list-group v-if="existSomeRoute(ruta.name)" :value="ruta.group" fluid> -->
                    <v-list-group :value="ruta.group" fluid :active="false">
                        <template v-slot:activator="{ props: groupProps }">
                            <v-list-item
                                v-bind="groupProps"
                                :title="ruta.title"
                                :prepend-icon="ruta.icon"
                                class="rounded-lg"
                            ></v-list-item>
                        </template>

                        <Link
                            v-for="groupItem in ruta.groupItems"
                            :key="groupItem.value"
                            :href="route(groupItem.name)"
                            preserve-scroll
                        >
                            <!-- v-if="can.includes(groupItem.name)" -->
                            <v-list-item
                                elevation="0"
                                variant="flat"
                                :active="route().current(groupItem.name)"
                                active-class="nav-item-active"
                                :prepend-icon="groupItem.icon"
                                :title="groupItem.title"
                                rounded="lg"
                                class="ml-2 transition-all rounded-lg"
                                :class="[
                                    route().current(groupItem.name)
                                        ? 'shadow-xl hover:shadow-2xl'
                                        : 'hover:shadow-2xl'
                                ]"
                            >
                            </v-list-item>
                        </Link>
                    </v-list-group>
                </div>
            </div>
        </v-list>
    </v-navigation-drawer>
</template>

<style scoped>
.v-list-item {
    background-color: rgb(var(--v-theme-customSurface));
    transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.v-list-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    background-color: rgba(var(--v-theme-customPrimary), 0.08);
}

.nav-item-active {
    background-color: rgb(var(--v-theme-customPrimary));
    color: rgb(var(--v-theme-customSurface));
}

.nav-item-active :deep(.v-icon) {
    color: currentColor !important;
}
</style>
