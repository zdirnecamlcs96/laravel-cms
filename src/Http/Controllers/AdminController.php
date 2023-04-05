<?php

namespace Local\CMS\Http\Controllers;

use Illuminate\Http\Request;
use Local\CMS\Models\Admin;
use Local\CMS\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Admin::class);
    }

    public function index()
    {
        $admins = Admin::all();
        return view('modules::admins.index', compact('admins'));
    }

    public function edit(Admin $admin)
    {
        $roles = Role::whereGuardName('admin')->pluck('name', 'id');
        return view('modules::admins.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $admin)
    {
        $this->validate($request, [
            'name' => "required|string",
            'email' => "required|email|unique:admins,email," . $admin->id,
            'password' => "nullable|min:8",
            'confirm_password' => "required_with:password|same:password",
            'roles' => [
                config('modules.role_management') ? 'required' : 'nullable',
            ]
        ]);

        $admin->update([
            "name" => $request->get('name'),
            "email" => $request->get('email'),
            "contact" => $request->get('contact'),
            "active" => $request->get('active'),
        ]);

        if($request->get('password')){
            $admin->update([
                "password" => bcrypt($request->get('password')),
            ]);
        }

        if (config('modules.role_management'))
        {
            $admin->syncRoles($request->get('roles'));
        }

        return redirect()->route('admin.admins.index')->withSuccess('Admin updated.');
    }

    public function create()
    {
        $roles = Role::whereGuardName('admin')->pluck('name', 'id');
        return view('modules::admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => "required|string",
            'email' => "required|email|unique:admins,email",
            'password' => "required|min:8",
            'confirm_password' => "required_with:password|same:password",
            'roles' => [
                config('modules.role_management') ? 'required' : 'nullable',
            ]
        ]);


        $admin = Admin::create([
            "name" => $request->get('name'),
            "email" => $request->get('email'),
            "contact" => $request->get('contact'),
            "active" => $request->get('active'),
            "password" => bcrypt($request->get('password')),
        ]);

        if (config('modules.role_management'))
        {
            $admin->syncRoles($request->get('roles'));
        }

        return redirect()->route('admin.admins.index')->withSuccess('Admin created.');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.admins.index')->withSuccess('Admin deleted.');
    }
}
