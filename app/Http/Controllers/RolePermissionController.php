<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user && $user->role && $user->role->is_access == 1) {
            $roles = Role::latest()->get();
        }else{
            $roles = Role::where('is_access',0)->latest()->get();
        }


        $menus = Menu::whereNull('parent_id')
            ->with('childrenRecursive') // Load all nested children
            ->orderBy('order', 'asc')
            ->get();
        return view('pages.role_permissions.index', compact('roles', 'menus'));
    }

    public function getPermissions($role_id)
    {
        $permissions = RolePermission::where('role_id', $role_id)->get(['menu_id', 'can_view', 'can_create', 'can_edit', 'can_delete']);
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        DB::beginTransaction(); // Start Transaction

        try {
            RolePermission::where('role_id', $request->role_id)->delete();
            if(isset($request->permissions) && !empty($request->permissions)){
                // Insert new permissions
                foreach ($request->permissions as $menu_id => $permission) {
                    RolePermission::create([
                        'role_id' => $request->role_id,
                        'menu_id' => $menu_id,
                        'can_view' => isset($permission['can_view']),
                        'can_create' => isset($permission['can_create']),
                        'can_edit' => isset($permission['can_edit']),
                        'can_delete' => isset($permission['can_delete']),
                    ]);
                }
            }

            DB::commit(); // Commit Transaction
            return redirect()->back()->with('success', 'Permissions updated successfully!');
        } catch (\Exception $e) {
            //return $e->getMessage();
            DB::rollBack(); // Rollback Transaction if Error Occurs
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
