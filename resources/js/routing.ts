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

    // ADMINISTRACIÓN
    {
        name: [
            'usuarios.index',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.show',
        ],
        title: 'Administración',
        icon: 'mdi-shield-account-outline',
        value: 'adminMenu',
        group: 'Administración',
        groupItems: [
            {
                name: 'usuarios.index',
                title: 'Usuarios',
                icon: 'mdi-account-multiple-outline',
                value: 'admin-users',
            },
        ],
    },

    // CATALOGOS
    {
        name: [
            'dependencias.index',
            'dependencias.create',
            'dependencias.edit',
            'dependencias.show',
        ],
        title: 'Catálogos',
        icon: 'mdi-database-outline',
        value: 'catalogosMenu',
        group: 'Catálogos',
        groupItems: [
            {
                name: 'dependencias.index',
                title: 'Dependencias',
                icon: 'mdi-office-building-outline',
                value: 'catalog-dependencias',
            },
        ],
    },
]

export default routes
