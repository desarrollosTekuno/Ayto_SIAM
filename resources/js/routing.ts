// resources/js/routing/routing.ts

interface Routing {
    name: string | string[]
    title: string
    icon: string
    value: string
    group: string | null
    groupItems?: Array<{
        name: string
        title: string
        icon: string
        value: string
    }>
}

const routes: Routing[] = [
    {
        name: 'dashboard',
        title: 'Inicio',
        icon: 'mdi-home-outline',
        value: 'dashboard',
        group: null,
    },

    {
        // Menú Administración
        name: [
            'Usuarios.index',
            'Usuarios.create',
            'Usuarios.edit',
        ],
        title: 'Administración',
        icon: 'mdi-shield-account-outline',
        value: 'adminMenu',
        group: 'Administración',
        groupItems: [
            {
                name: 'Usuarios.index',
                title: 'Usuarios',
                icon: 'mdi-account-multiple-outline',
                value: 'admin-users',
            },
            // {
            //     name: 'Roles.index',
            //     title: 'Roles',
            //     icon: 'mdi-key-outline',
            //     value: 'admin-roles',
            // },
            // {
            //     name: 'Configuracion.index',
            //     title: 'Configuración',
            //     icon: 'mdi-cog-outline',
            //     value: 'admin-settings',
            // },
        ],
    },
]

export default routes
