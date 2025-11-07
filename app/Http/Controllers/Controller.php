<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Ensure the authenticated user has the required permission(s). Admin and super admin
     * roles are treated as full-access overrides.
     *
     * @param  string|string[]  $permission
     */
    protected function ensurePermission(Request $request, string|array $permission): void
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'You do not have permission to perform this action.');
        }

        $role = strtolower((string) ($user->role ?? ''));
        if (in_array($role, ['admin', 'super_admin'], true)) {
            return;
        }

        $permissions = (array) $permission;
        foreach ($permissions as $entry) {
            if ($entry !== '' && $user->can($entry)) {
                return;
            }
        }

        abort(403, 'You do not have permission to perform this action.');
    }
}
