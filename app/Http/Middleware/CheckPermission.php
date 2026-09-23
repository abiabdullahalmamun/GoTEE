<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RolePermission;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'You must be logged in.');
        }

        // Super admin access
        // return $user ; 
        if ($user && $user->role && $user->role->is_access == 1) {
            return $next($request);
        }

        $routeName = $request->route()->getName(); // Get the current route name
        $baseRoute = $routeName;

        // Automatically allow ".show" if ".index" is permitted
        if (str_ends_with($routeName, '.show')) {
            $baseRoute = str_replace('.show', '.index', $routeName);
        }
        // Allow ".create", ".edit", ".delete" only if explicitly granted in `role_permissions`
        elseif (str_ends_with($routeName, '.create')) {
            $baseRoute = str_replace('.create', '.index', $routeName);
            $permission = 'can_create';
        }
        elseif (str_ends_with($routeName, '.store')) {
            $baseRoute = str_replace('.store', '.index', $routeName);
            $permission = 'can_create';
        }
        elseif (str_ends_with($routeName, '.edit')) {
            $baseRoute = str_replace('.edit', '.index', $routeName);
            $permission = 'can_edit';
        }
        elseif (str_ends_with($routeName, '.update')) {
            $baseRoute = str_replace('.update', '.index', $routeName);
            $permission = 'can_edit';
        }
        elseif (str_ends_with($routeName, '.delete')) {
            $baseRoute = str_replace('.delete', '.index', $routeName);
            $permission = 'can_delete';
        }
        elseif (str_ends_with($routeName, '.destroy')) {
            $baseRoute = str_replace('.destroy', '.index', $routeName);
            $permission = 'can_delete';
        }   
        elseif (str_ends_with($routeName, '.assign')) {
            $baseRoute = str_replace('.assign', '.index', $routeName);
            $permission = 'can_edit'; // Treat as update permission
        }  //added jun 23, 2025 abdullah
        elseif (str_ends_with($routeName, '.updateAssign')) {
            $baseRoute = str_replace('.updateAssign', '.index', $routeName);
            $permission = 'can_edit'; // Treat as update permission
        }  //added jun 23, 2025 abdullah
        elseif (str_ends_with($routeName, '.search')) {
            $baseRoute = str_replace('.search', '.index', $routeName);
            $permission = 'can_view'; // Treat as update permission
        }  //added Aug 2, 2025 abdullah
       elseif (str_ends_with($routeName, '.summary')) {
            $baseRoute = str_replace('.summary', '.index', $routeName);
            $permission = 'can_view'; // Treat as update permission
        }  //added Oct 10, 2025 abdullah
        elseif (str_ends_with($routeName, '.excel')) {
            $baseRoute = str_replace('.excel', '.index', $routeName);
            $permission = 'can_view'; // Treat as update permission
        }  //added Aug 2, 2025 abdullah
          elseif (str_ends_with($routeName, '.pdf')) {
            $baseRoute = str_replace('.pdf', '.index', $routeName);
            $permission = 'can_view'; // Treat as update permission
        }  //added Aug 2, 2025 abdullah

        $hasPermission = RolePermission::where('role_id', $user->role_id)
            ->whereHas('menu', function ($query) use ($baseRoute) {
                $query->where('route', $baseRoute);
            })
            ->where($permission, 1) // Check for the specific permission
            ->exists();

        if (!$hasPermission) {
                if (str_ends_with($routeName, '.create')) {
                    $baseRoute = str_replace('.create', '.create', $routeName);
                    $permission = 'can_create';
                }
                elseif (str_ends_with($routeName, '.edit')) {
                    $baseRoute = str_replace('.edit', '.edit', $routeName);
                    $permission = 'can_edit';
                }
                elseif (str_ends_with($routeName, '.delete')) {
                    $baseRoute = str_replace('.delete', '.delete', $routeName);
                    $permission = 'can_delete';
                }
                elseif (str_ends_with($routeName, '.destroy')) {
                    $baseRoute = str_replace('.destroy', '.destroy', $routeName);
                    $permission = 'can_delete';
                }
                elseif (str_ends_with($routeName, '.assign')) {
                    $baseRoute = str_replace('.assign', '.index', $routeName);
                    $permission = 'can_edit'; // Treat as update permission
                }  //added jun 23, 2025 abdullah
                elseif (str_ends_with($routeName, '.updateAssign')) {
                    $baseRoute = str_replace('.updateAssign', '.index', $routeName);
                    $permission = 'can_edit'; // Treat as update permission
                }  //added jun 23, 2025 abdullah

                $hasPermission = RolePermission::where('role_id', $user->role_id)
                    ->whereHas('menu', function ($query) use ($baseRoute) {
                        $query->where('route', $baseRoute);
                    })
                    ->where($permission, 1) // Check for the specific permission
                    ->exists();

                if ($hasPermission) {
                    return $next($request);
                }
            return redirect('/permission-denied'); // Redirect to custom error page
        }

        return $next($request);
    }

}
