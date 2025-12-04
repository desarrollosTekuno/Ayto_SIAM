// resources/js/plugins/vuetify.js
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { mdi } from "vuetify/iconsets/mdi";

import { VDateInput } from "vuetify/labs/VDateInput";

import DateFnsAdapter from "@date-io/date-fns";
import enUS from "date-fns/locale/en-US";
import es from "date-fns/locale/es";

export const vuetify = createVuetify({
    components: {
        ...components,
        VDateInput,
    },
    directives,
    icons: {
        defaultSet: "mdi",
        sets: { mdi },
    },
    date: {
        adapter: DateFnsAdapter,
        locale: { es: es, en: enUS },
    },
    theme: {
        defaultTheme: "myTheme",
        themes: {
            myTheme: {
                dark: false,
                colors: {
                    customPrimary: "#0C1A2C",
                    customSecondary: "#D9D9D9",
                    customThird: "#F1EFE8",
                    customFourth: "#E03A3E",
                    customFifth: "#B0B0B0",
                },
            },
            myDarkTheme: {
                dark: true,
                colors: {
                    primary: "#F1EFE8",
                    background: "#B0B0B0",
                    drawer: "#E03A3E",
                    customPrimary: "#F0F0F0",
                },
            },
        },
    },
});

export default vuetify;
