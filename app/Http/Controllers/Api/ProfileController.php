<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * JSON counterpart of the web ProfileController, for the mobile app.
 * Same rules and behaviour — just returns resources instead of Blade
 * views / redirects. Mounted under auth:sanctum in routes/api.php.
 */
class ProfileController extends Controller
{
    /** GET /api/profile — the currently authenticated user. */
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    /** PUT /api/profile — update own name + email. */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'User_Name' => [
                'required', 'string', 'max:40',
                Rule::unique('users', 'User_Name')->ignore($user->User_ID, 'User_ID'),
            ],
            'User_Email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'User_Email')->ignore($user->User_ID, 'User_ID'),
            ],
        ]);

        $user->User_Name  = $validated['User_Name'];
        $user->User_Email = $validated['User_Email'];
        $user->save();

        return response()->json([
            'success' => true,
            'data'    => new UserResource($user),
            'message' => 'Profile updated successfully.',
        ]);
    }

    /** PUT /api/profile/password — verify current, set new. */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->User_Password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors'  => ['current_password' => ['Your current password is incorrect.']],
            ], 422);
        }

        $user->User_Password = $validated['new_password']; // 'hashed' cast on the model
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}
