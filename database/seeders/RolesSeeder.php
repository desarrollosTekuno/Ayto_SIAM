<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder {

    public function run(): void {
        // ================= ROLES =================
        $roles = [
            'SUPERADMINISTRADOR',
            'ANALISTA',
            'OPERADOR',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        // ================= MODULOS =================
        $modulos = [
            'RolesPermisos',
            'usuarios',
            'cargos',
            'titulares',
            'secretarias',
            'dependencias',
            'unidades_administrativas',
            'areas',
            'lineamientos',
            'configuraciones_sistema',
            // 'tipos_procesos',
            'tipos_procedimientos',
            'rangos_procedimientos',
            'archivos_permitidos_proceso',
        ];

        $acciones = [
            'index', //inicio
            'show', //ver
            'store', //guardar
            'update', //actualizar
            'destroy', //eliminar
            'other' //otros
        ];

        // ================= PERMISOS =================
        $permissions = [];

        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                $permissions[] = "{$modulo}.{$accion}";
            }
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // ================= ASIGNACION =================
        $superadmin = Role::where('name', 'SUPERADMINISTRADOR')->first();
        $analista   = Role::where('name', 'ANALISTA')->first();
        $operador   = Role::where('name', 'OPERADOR')->first();

        $superadmin->syncPermissions($permissions);

        $analista->syncPermissions(
            collect($permissions)->reject(fn ($p) => str_ends_with($p, '.destroy'))
        );

        $operador->syncPermissions(
            collect($permissions)->filter(fn ($p) => str_ends_with($p, '.index'))
        );
    }
}
