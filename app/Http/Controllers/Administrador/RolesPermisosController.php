<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;


class RolesPermisosController extends Controller {

    public function index() {
        $Roles = $this->Roles();
        $Permisos = Permission::get();

        return Inertia::render('Administrador/Roles&Permisos', compact('Roles', 'Permisos'));
    }

    public function Roles() {
        return Role::with('permissions')->get();
    }

    public function PermisosAsigados(Request $request) {
        $role = Role::findOrFail($request->id);
        $PermisosAsignados = $role->permissions;
        return $PermisosAsignados;
    }

    public function store(Request $request) {
        if ($request->Vista == 1) {

            $request->validate([
                'name' => 'required|unique:roles,name'
            ], [
                'name.required' => 'El nombre del rol es obligatorio',
                'name.unique' => 'Este nombre de rol ya existe'
            ]);

            $role = Role::create([
                'name' => $request->name
            ]);
            $role->syncPermissions($request->permissions);

        } else {
            $request->validate([
                'name' => 'required|unique:permissions,name',
            ]);

            Permission::create([
                'name' => $request->name
            ]);
        }

        return redirect()->back()->with('success', 'Dato creado correctamente');
    }

    public function update(Request $request, $id) {
        if ($request->Vista == 1) {
            $role = Role::findOrFail($id);

            $request->validate([
                'name' => ['required', 'unique:roles,name,' . $role->id],
            ], [
                'name.required' => 'El nombre del rol es obligatorio',
                'name.unique' => 'Este nombre de rol ya existe'
            ]);

            $role->update([
                'name' => $request->name
            ]);
            $role->syncPermissions($request->permissions);

        } else {
            $permission = Permission::findOrFail($id);

            $request->validate([
                'name' => 'required|unique:permissions,name,' . $permission->id,
            ]);

            $permission->update([
                'name' => $request->name
            ]);
        }

        return redirect()->back()->with('success', 'Actualizado correctamente');
    }

    public function destroy($id) {
        $vista = request()->input('Vista');
        if ($vista == 1) {
            Role::findOrFail($id)->delete();
        } else {
            Permission::findOrFail($id)->delete();
        }

        return redirect()->back()->with('success', 'Eliminado correctamente');
    }

    public function AsignarPermisos(Request $request) {
        $role = Role::findOrFail($request->rol_id);

        if ($request->assign) {
            $role->givePermissionTo($request->permiso_id);
        } else {
            $role->revokePermissionTo($request->permiso_id);
        }

        return response()->json(['success' => true, 'message' => 'Permiso actualizado correctamente']);
    }
}
