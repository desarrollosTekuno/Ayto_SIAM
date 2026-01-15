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
                name: 'RolesPermisos.index',
                title: 'Roles y Permisos',
                icon: 'mdi mdi-card-bulleted-settings',
                value: 'admin-users',
            },
            {
                name: 'usuarios.index',
                title: 'Usuarios',
                icon: 'mdi-account-multiple-outline',
                value: 'admin-users',
            },

            {
                name: 'configuraciones_sistema.index',
                title: 'Configuración',
                icon: 'mdi-cog-outline',
                value: 'admin-configuraciones_sistema',
            },

            {
                name: 'tipos_procesos.index',
                title: 'Tipos de Procesos',
                icon:   'mdi mdi-card-bulleted-settings',
                value: 'admin-tipos_procesos',
            },

            {
                name: 'rangos_procedimientos.index',
                title: 'Rangos de Procedimientos',
                icon: 'mdi mdi-card-bulleted-settings',
                value: 'admin-rangos_procedimientos',
            },

            {
                name: 'archivos_permitidos.index',
                title: 'Archivos Permitidos',
                icon: 'mdi mdi-card-bulleted-settings',
                value: 'admin-archivos_permitidos',
            }
        ],
    },

    // CATALOGOS
    {
        name: [
            'cargos.index',
            'cargos.create',
            'cargos.edit',
            'cargos.show',

            'dependencias.index',
            'dependencias.create',
            'dependencias.edit',
            'dependencias.show',

            'unidades_administrativas.index',
            'unidades_administrativas.create',
            'unidades_administrativas.edit',
            'unidades_administrativas.show',

            'areas.index',
            'areas.create',
            'areas.edit',
            'areas.show',

            'departamentos.index',
            'departamentos.create',
            'departamentos.edit',
            'departamentos.show',

            'lineamientos.index',
            'lineamientos.create',
            'lineamientos.edit',
            'lineamientos.show',

            'titulares.index',
            'titulares.create',
            'titulares.edit',
            'titulares.show',
        ],
        title: 'Catálogos',
        icon: 'mdi-database-outline',
        value: 'catalogosMenu',
        group: 'Catálogos',
        groupItems: [
            {
                name: 'cargos.index',
                title: 'Cargos',
                icon: 'mdi-account-tie-outline',
                value: 'catalog-cargos',
            },

            {
                name: 'titulares.index',
                title: 'Personas Titulares',
                icon: 'mdi-account-tie-outline',
                value: 'catalog-titulares',
            },

            // {
            //     name: 'secretarias.index',
            //     title: 'Secretarias',
            //     icon: 'mdi-account-tie-outline',
            //     value: 'catalog-scretarias',
            // },

            {
                name: 'dependencias.index',
                title: 'Dependencias',
                icon: 'mdi-office-building-outline',
                value: 'catalog-dependencias',
            },

            {
                name: 'unidades_administrativas.index',
                title: 'Unidades Administrativas',
                icon: 'mdi-office-building-cog-outline',
                value: 'catalog-unidades_administrativas',
            },

            {
                name: 'lineamientos.index',
                title: 'Formatos Lineamientos',
                icon: 'mdi-file-document-outline',
                value: 'catalog-lineamientos',
            },

            // {
            //     name: 'areas.index',
            //     title: 'Áreas',
            //     icon: 'mdi-domain-outline',
            //     value: 'catalog-areas',
            // },

            // {
            //     name: 'departamentos.index',
            //     title: 'Departamentos',
            //     icon: 'mdi-office-building-cog-outline',
            //     value: 'catalog-departamentos',
            // },
        ],
    },

]

export default routes
