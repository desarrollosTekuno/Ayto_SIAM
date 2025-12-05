import { Inertia } from "@inertiajs/inertia";
import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

const appName = import.meta.env.VITE_APP_NAME || "SIAM";

// Iconos y estilos
import "@mdi/font/css/materialdesignicons.css";
import "sweetalert2/dist/sweetalert2.min.css";

// Plugins externos
import VueSweetalert2 from "vue-sweetalert2";
import vue3Spinner from "vue3-spinner";

// Plugins propios
import vuetify from "./plugins/vuetify";
import sweetalertOptions from "./plugins/sweetalert";

//Roles y permisos
import AuthGlobals from "./plugins/auth-globals";

import FormValidate from '@/Components/FormValidate.vue';

import { isLoading } from "./loading";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .use(vue3Spinner)
            .use(VueSweetalert2, sweetalertOptions)
            .component('FormValidate', FormValidate)
            .use(AuthGlobals);

        Inertia.on("start", () => (isLoading.value = true));
        Inertia.on("finish", () => (isLoading.value = false));

        return vueApp.mount(el);
    },
    progress: {
        color: '#7D2447',
    },
});
