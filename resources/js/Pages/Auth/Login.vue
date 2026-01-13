<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'

import AuthenticationCard from '@/Components/AuthenticationCard.vue'
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdPasswordInput from '@/Components/MaterialDesign/MdPasswordInput.vue'
import Loader from '@/Components/Loader.vue'
import VDialog from '@/Components/Vuetify/VDialog.vue'
import VBtnCancel from '@/Components/Vuetify/VBtnCancel.vue'
import { isLoading } from '@/loading'
import { errorToast } from '@/utils/swal'

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    loginField: String, // 'username' | 'email'
})

const form = useForm({
    username: '',
    email: '',
    password: '',
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}

// =================== Lineamientos modal ===================
const showLineamientos = ref(false)
const loadingLineamientos = ref(false)
const formatos = ref([])

const AbrirLineamientos = async () => {
    showLineamientos.value = true
    loadingLineamientos.value = true
    formatos.value = []

    try {
        const { data } = await axios.get(route('formatos.visualizar'), {
            headers: { Accept: 'application/json' },
        })
        formatos.value = data ?? []
    } catch (e) {
        errorToast('No se pudieron cargar los lineamientos')
    } finally {
        loadingLineamientos.value = false
    }
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="h-16"></div>

        <div class="mb-6 text-center">
            <button
                type="button"
                class="text-sm text-blue-600 underline"
                @click="AbrirLineamientos"
            >
                Lineamientos generales
            </button>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <MdTextInput
                v-model="form[loginField]"
                :label="loginField === 'email' ? 'Correo electrónico' : 'Usuario'"
                :icon="loginField === 'email' ? 'mdi-email' : 'mdi-account'"
                required
                :external-error="form.errors[loginField]"
            />

            <MdPasswordInput
                v-model="form.password"
                label="Contraseña"
                icon="mdi-key"
                required
                :external-error="form.errors.password"
                class="my-4"
            />

            <div class="text-center">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-blue-600 underline"
                >
                    Restablecer contraseña
                </Link>
            </div>

            <v-btn
                type="submit"
                block
                size="large"
                class="mt-2 text-white"
                style="background-color:#7A1F3D"
            >
                Ingresar
            </v-btn>
        </form>

        <Loader :overlay="isLoading" />

        <!-- MODAL LINEAMIENTOS -->
        <VDialog
            v-model="showLineamientos"
            title="Lineamientos generales"
            header-icon="mdi-file-document-outline"
            max-width="500"
        >
            <template #content>
                <div v-if="loadingLineamientos" class="pt-4 text-center text-slate-600">
                    Cargando…
                </div>

                <div v-else>
                    <div class="px-4 overflow-y-auto h-74">
                        <div v-for="f in formatos" :key="f.id" class="flex items-center justify-between gap-4 px-3 py-1 mb-1 transition bg-white border shadow-sm group rounded-xl border-slate-200 hover:border-slate-300 hover:shadow">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-700" aria-hidden="true">
                                    <i class="mdi mdi-file-document-outline" aria-hidden="true"></i>
                                </span>

                                <div class="min-w-0">
                                    <div class="text-base font-semibold truncate text-slate-900">
                                        {{ f.nombre }}
                                    </div>

                                    <div v-if="f.titulo" class="mt-0.5 truncate text-sm text-slate-600">
                                        {{ f.titulo }}
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a
                                v-if="f.archivo"
                                :href="`/storage/${f.archivo}`"
                                target="_blank"
                                rel="noopener"
                                class="px-4 py-1 mx-4 text-white rounded-lg bg-customPrimary"
                                title="Abrir en una pestaña nueva"
                                >
                                    <span> <span class="mdi mdi-tray-arrow-down"></span> Abrir archivo</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </template>

            <template #footer="{ close }">
                <VBtnCancel prepend-icon="mdi-close" @click="close">
                    Cerrar
                </VBtnCancel>
            </template>
        </VDialog>
    </AuthenticationCard>
</template>
