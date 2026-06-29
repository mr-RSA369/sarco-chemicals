<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
        {
            return User::select(
                'id',
                'name',
                'email',
                'role',
                'created_at'
            )->latest()->get();
        }

    public function updateRole(Request $request, User $user)
        {
            $request->validate([
                'role' => 'required|in:manager,admin,user'
            ]);

            $user->update([
                'role' => $request->role
            ]);

            return response()->json([
                'message' => 'Role updated.'
            ]);
        }

    public function destroy(User $user)
        {
            if ($user->id == auth()->id()) {

                return response()->json([
                    'message' => 'You cannot delete yourself.'
                ], 422);

            }

            $user->delete();

            return response()->json([
                'message' => 'User deleted.'
            ]);
        }
}
