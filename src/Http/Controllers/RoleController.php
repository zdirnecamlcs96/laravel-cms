<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Local\CMS\Models\Permission;
use Local\CMS\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class);
    }

    public function index()
    {
        $roles = Role::all();
        return view('modules::roles.index', compact('roles'));
    }

    public function create()
    {
        return view('modules::roles.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ["required", "string", Rule::unique('roles', 'name')],
        ]);

        $permissions = Permission::whereIn('name', $this->getPermissionsFromRequest())
            ->where('guard_name', 'admin')
            ->get();

        $role = Role::create([
            "name" => $request->get('name'),
        ]);

        $role->syncPermissions($permissions->pluck('id'));

        return redirect()->route('admin.roles.edit', $role)->withSuccess('Role created.');
    }

    public function edit(Role $role)
    {
        return view('modules::roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name' => ["required", "string", Rule::unique('roles', 'name')->ignore($role)],
        ]);

        $permissions = Permission::whereIn('name', $this->getPermissionsFromRequest())
            ->where('guard_name', 'admin')
            ->get();

        $role->update([
            "name" => $request->get('name'),
        ]);

        $role->syncPermissions($permissions->pluck('id'));

        return redirect()->route('admin.roles.edit', $role)->withSuccess('Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->withSuccess('Role deleted.');
    }

    private function getPermissionsFromRequest()
    {
        return array_keys(array_filter(request()->get('permissions'), fn($item) => $item == "true"));
    }
}
