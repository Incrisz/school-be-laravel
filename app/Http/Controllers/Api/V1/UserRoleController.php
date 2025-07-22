<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($user->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $user->roles()->syncWithoutDetaching($request->role_id);

        return response()->json(['message' => 'Role assigned successfully.']);
    }

    public function unassignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($user->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $user->roles()->detach($request->role_id);

        return response()->json(['message' => 'Role unassigned successfully.']);
    }
}
