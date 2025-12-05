<?php

namespace App\Http\Controllers\UserManager;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// class RolesController extends Controller
class RolesController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:عرض صلاحية')->only('index','show');
        $this->middleware('permission:اضافة صلاحية')->only('create','store');
        $this->middleware('permission:تعديل صلاحية')->only('edit','update');
        $this->middleware('permission:حذف صلاحية')->only('destroy');
    }
    public function index()
    {
        $roles = Role::select('id','name')->get();
        return view('roles.index', compact('roles'));
    }
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    public function create()
    {
        $permission = Permission::select('id','name')->get();
        return view('roles.create', compact('permission'));
    }
    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();
        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permission']);
        return redirect()->route('roles.index')->with('success', 'تم انشاء الصلاحية بنجاح');
    }
    public function edit(Role $role)
    {
        $permission = Permission::select('id','name')->get();
        $role_permissions = $role->permissions->pluck('name')->toArray();
        // return $role_permissions;
        return view('roles.edit', compact('role','permission','role_permissions'));
    }
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permission']);
        return redirect()->route('roles.index')->with('success', 'تم تعديل الصلاحية بنجاح');
    }
    
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'تم انشاء الصلاحية بنجاح');
    }
}
