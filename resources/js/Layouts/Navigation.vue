<script setup lang="ts">
import routes from '@/routing'
import { Link, usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

type RouteName = string | string[]

const page = usePage() as any
const auth = page.props?.auth ?? { user: {}, permissions: [] }
// const can = (auth.permissions ?? []) as string[]

const drawer = defineModel<boolean>('drawer')
const props = defineProps<{ rail: boolean }>()

const opened = ref<string[]>([])

// const existSomeRoute = (routeNames: RouteName): boolean =>
//     Array.isArray(routeNames)
//         ? routeNames.some((name) => can.includes(name))
//         : can.includes(routeNames)

const existSomeRoute = (_routeNames: RouteName): boolean => true

const route = (window as any).route as any

const onUpdateOpened = (newOpened: string[]) => {
    opened.value = newOpened
}

onMounted(() => {
    opened.value =
        routes
            .filter((ruta: any) =>
                ruta.groupItems?.some((groupItem: any) => route().current(groupItem.name)),
            )
            .map((ruta: any) => ruta.group) ?? []
})
</script>

<template>
    <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        :permanent="props.rail"
        :rail="$vuetify.display.mobile ? !props.rail : props.rail"
        theme="myDarkTheme"
        class="font-poppins bg-customPrimary"
    >
        <v-list>
            <v-list-item
                class="py-2 bg-customThird"
                prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                :subtitle="auth.user?.email"
                :title="auth.user?.name"
            />
        </v-list>

        <v-divider />

        <v-list
            class="bg-[url(/assets/images/Logo.png)]"
            open-strategy="multiple"
            :opened="opened"
            @update:opened="onUpdateOpened"
            density="comfortable"
            nav
        >
            <div v-for="ruta in routes" :key="ruta.value">
                <div v-if="ruta.group == null">
                    <Link v-if="existSomeRoute(ruta.name)" :href="route(ruta.name)" preserve-scroll>
                        <v-list-item
                            elevation="0"
                            variant="elevated"
                            rounded="pill"
                            :title="ruta.title"
                            active-color="customSecondary"
                            base-color="customPrimary"
                            :active="
                                Array.isArray(ruta.name)
                                    ? route().current(ruta.name[0])
                                    : route().current(ruta.name)
                            "
                        >
                            <template #prepend>
                                <v-icon :icon="ruta.icon" />
                            </template>
                        </v-list-item>
                    </Link>
                </div>

                <div v-else>
                    <v-list-group v-if="existSomeRoute(ruta.name)" :value="ruta.group" fluid>
                        <template #activator="{ props: slotProps }">
                            <v-list-item
                                color="customSecondary"
                                v-bind="slotProps"
                                :title="ruta.title"
                                :prepend-icon="ruta.icon"
                            />
                        </template>

                        <Link
                            v-for="groupItem in ruta.groupItems"
                            :key="groupItem.value"
                            :href="route(groupItem.name)"
                            preserve-scroll
                        >
                            <!-- v-if="can.includes(groupItem.name)" añadir despues -->
                            <v-list-item

                                class="ml-2"
                                elevation="0"
                                variant="elevated"
                                color="customSecondary"
                                rounded="pill"
                                :active="route().current(groupItem.name)"
                                :prepend-icon="groupItem.icon"
                                :title="groupItem.title"
                                active-color="customSecondary"
                                base-color="customPrimary"
                            />
                        </Link>
                    </v-list-group>
                </div>
            </div>

            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="flex items-start justify-start w-full bg-customPrimary"
            >
                <v-list-item
                    elevation="0"
                    variant="elevated"
                    base-color="customPrimary"
                    rounded="pill"
                    :active="route().current('logout')"
                    prepend-icon="mdi-logout"
                    title="Cerrar Sesión"
                />
            </Link>
        </v-list>
    </v-navigation-drawer>
</template>
