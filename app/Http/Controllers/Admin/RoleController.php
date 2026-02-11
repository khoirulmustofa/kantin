<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->rows ?? 10;
            $roles = Role::withCount(['permissions', 'users'])
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            // Handle Multi Sort
            $multiSortMeta = $request->input('multiSortMeta');
            if ($multiSortMeta) {
                $sorts = json_decode($multiSortMeta, true);
                if (is_array($sorts)) {
                    foreach ($sorts as $sort) {
                        $direction = ($sort['order'] ?? 1) === 1 ? 'asc' : 'desc';
                        $field = $sort['field'] ?? 'created_at';
                        $roles->orderBy($field, $direction);
                    }
                }
            } else {
                $roles->latest();
            }
            $roles = $roles->paginate($perPage)
                ->withQueryString();


            return Inertia::render('Admin/Role/Index', [
                'menu' => 'roles',
                'title' => 'Roles',
                'roles' => $roles,
                'filters' => $request->only(['search']),
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function getUsers(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);
            $users = \App\Models\User::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });

            // Handle Multi Sort
            $multiSortMeta = $request->input('multiSortMeta');
            if ($multiSortMeta) {
                $sorts = json_decode($multiSortMeta, true);
                if (is_array($sorts)) {
                    foreach ($sorts as $sort) {
                        $direction = ($sort['order'] ?? 1) === 1 ? 'asc' : 'desc';
                        $field = $sort['field'] ?? 'created_at';
                        $users->orderBy($field, $direction);
                    }
                }
            } else {
                $users->latest();
            }

            $users = $users->paginate($request->rows ?? 10)
                ->through(function ($user) use ($role) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'has_role' => $user->hasRole($role->name)
                    ];
                });

            return response()->json([
                'status' => true,
                'users' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function toggleUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:assign,remove'
        ]);

        try {
            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $user = \App\Models\User::findOrFail($request->user_id);

            if ($request->action === 'assign') {
                $user->assignRole($role->name);
            } else {
                if ($role->name === 'Admin' && $user->id === auth()->id()) {
                    return response()->json(['status' => false, 'message' => 'Cannot remove your own Admin role'], 400);
                }
                $user->removeRole($role->name);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Role updated successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        try {
            $permissions = Permission::all()->groupBy('group');

            return Inertia::render('Admin/Role/Create', [
                'menu' => 'roles',
                'title' => 'Create Role',
                'permissions' => $permissions,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            $role->syncPermissions($request->permissions);

            DB::commit();

            return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $role = Role::findOrFail($id);
            $permissions = Permission::all()->groupBy('group');
            $rolePermissions = $role->permissions->pluck('name')->toArray();

            return Inertia::render('Admin/Role/Edit', [
                'menu' => 'roles',
                'title' => 'Edit Role',
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            DB::commit();

            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->name === 'Admin') {
                return redirect()->back()->with('error', 'Cannot delete Admin role');
            }

            $role->delete();

            return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
