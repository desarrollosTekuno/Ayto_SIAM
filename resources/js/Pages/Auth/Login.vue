<script setup>
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import PasswordField from "@/Components/PasswordField.vue";
import InputError from "@/Components/InputError.vue";
import Loader from "@/Components/Loader.vue";
import { isLoading } from "@/loading";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    loginField: String, // "username" o "email"
});

const form = useForm({
    username: "",
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            remember: form.remember ? "on" : "",
        }))
        .post(route("login"), {
            onFinish: () => form.reset("password"),
        });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Mensaje de estado -->
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <v-text-field
                    required
                    v-model="form[loginField]"
                    :prepend-inner-icon="loginField === 'email' ? 'mdi-email' : 'mdi-account'"
                    :label="loginField === 'email' ? 'Correo electrónico' : 'Usuario'"
                    variant="outlined"
                    :type="loginField === 'email' ? 'email' : 'text'"
                    :autocomplete="loginField"
                ></v-text-field>

                <InputError class="mt-2" :message="form.errors[loginField]" />
            </div>

            <!-- Contraseña -->
            <div class="mt-4">
                <PasswordField
                    v-model="form.password"
                    label="Contraseña"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Recordarme -->
            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="text-sm text-gray-600 ms-2 dark:text-gray-400">
                        Recuérdame
                    </span>
                </label>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 underline rounded-md dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    ¿Olvidaste tu contraseña?
                </Link>

                <v-btn type="submit" prepend-icon="mdi-login">
                    Ingresar
                </v-btn>
            </div>

        </form>

        <Loader :overlay="isLoading" />
    </AuthenticationCard>
</template>
