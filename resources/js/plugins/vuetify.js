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
            customPrimary: "#7d2447",
            customPrimaryDark: "#6a1f3c",
            customSecondary: "#41504d",
            customTertiary: "#636569",
            customSurface: "#f1f0f0",
            customMuted: "#bfb591",
            customDark: "#0d0d0d",
        },
        },
        myDarkTheme: {
        dark: true,
        colors: {
            customPrimary: "#41504d",
            customSecondary: "#bfb591",
            customSurface: "#0d0d0d",
        },
        },
    },
    }

});

export default vuetify;
