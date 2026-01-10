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
        name: [
            'Usuarios.index',
            'Usuarios.create',
            'Usuarios.edit',
            'Usuarios.show',
        ],
            title: 'Administracion',
            icon: 'mdi-shield-account-outline',
            value: 'adminMenu',
            group: 'Administracion',
        groupItems: [
        {
            name: 'Usuarios.index',
            title: 'Usuarios',
            icon: 'mdi-account-multiple-outline',
            value: 'admin-users',
        },
        ],
    },
]

export default routes
