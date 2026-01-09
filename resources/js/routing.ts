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
        // Menú Administrador (mock para probar)
        name: ['admin.users.index', 'admin.roles.index', 'admin.settings.index'],
        title: 'Administración',
        icon: 'mdi-shield-account-outline',
        value: 'adminMenu',
        group: 'Administración',
        groupItems: [
        {
            name: 'admin',
            title: 'Usuarios',
            icon: 'mdi-account-multiple-outline',
            value: 'admin-users',
        },
        {
            name: 'admin',
            title: 'Permisos',
            icon: 'mdi-key-outline',
            value: 'admin-permissions',
        },
        {
            name: 'admin',
            title: 'Configuración',
            icon: 'mdi-cog-outline',
            value: 'admin-settings',
        },
        ],
    },
]

export default routes
