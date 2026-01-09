<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticationCard from '@/Components/AuthenticationCard.vue'
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdPasswordInput from '@/Components/MaterialDesign/MdPasswordInput.vue'
import Loader from '@/Components/Loader.vue'
import { isLoading } from '@/loading'

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
</script>

<template>
    <Head title="Iniciar sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="h-16"></div>

        <div class="mb-6 text-center">
            <a
                href="#"
                class="text-sm text-blue-600 underline"
            >
                Lineamientos generales
            </a>
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
    </AuthenticationCard>
</template>
