<script setup lang="ts">
import routes from '@/routing'
import { Link, usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
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
            density="comfortable"
            nav
        >
            <div v-for="ruta in routes" :key="ruta.value">
                <div v-if="ruta.group == null">
                    <!-- <Link v-if="existSomeRoute(ruta.name)" :href="route(Array.isArray(ruta.name) ? ruta.name[0] : ruta.name)" preserve-scroll> -->
                    <Link :href="route(Array.isArray(ruta.name) ? ruta.name[0] : ruta.name)" preserve-scroll>
                        <v-list-item
                            elevation="0"
                            variant="elevated"
                            :title="ruta.title"
                            base-color="customSurface"
                            :active="ruta.name instanceof Array ? route().current(ruta.name[0]) : route().current(ruta.name)"
                        >
                            <template #prepend>
                                <v-icon :icon="ruta.icon"></v-icon>
                            </template>
                        </v-list-item>
                    </Link>
                </div>

                <div v-else>
                    <!-- <v-list-group v-if="existSomeRoute(ruta.name)" :value="ruta.group" fluid> -->
                    <v-list-group :value="ruta.group" fluid>
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                color="customSecondary"
                                v-bind="props"
                                :title="ruta.title"
                                :prepend-icon="ruta.icon"
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
                                elevation="0 ml-2"
                                variant="elevated"
                                color="customPrimaryDark"
                                :active="route().current(groupItem.name)"
                                :prepend-icon="groupItem.icon"
                                :title="groupItem.title"
                                base-color="customPrimary"
                                rounded="shaped"
                            >
                            </v-list-item>
                        </Link>
                    </v-list-group>
                </div>
            </div>
        </v-list>
    </v-navigation-drawer>
</template>
