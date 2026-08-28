<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * JSON counterpart of the web UserManagementController, for the mobile
 * app's "Manage Users" tab. Admin-only: every action 403s for a
 * non-Admin token. Mounted under auth:sanctum in routes/api.php.
 */
class UserManagementController extends Controller
{
    /** Roles assignable through this panel — single source of truth. */
    protected array $roles = ['Admin', 'Staff', 'Guest'];

    /** Reject anyone who isn't an Admin before any action runs. */
    private function ensureAdmin(Request $request): void
    {
        abort_unless(
            $request->user() && $request->user()->User_Role === 'Admin',
            403,
            'Admin access is required to manage users.'
        );
    }

    /** GET /api/users?page=N — paginated list, 15 per page. */
    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $users = User::orderBy('User_Name')->paginate(15);

        return UserResource::collection($users);
    }

    /** POST /api/users — create a user. */
    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'User_Name'     => ['required', 'string', 'max:40', Rule::unique('users', 'User_Name')],
            'User_Email'    => ['required', 'email', 'max:255', Rule::unique('users', 'User_Email')],
            'User_Password' => ['required', 'string', 'min:8'],
            'User_Role'     => ['required', Rule::in($this->roles)],
            'Status'        => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $user = User::create([
            'User_Name'     => $validated['User_Name'],
            'User_Email'    => $validated['User_Email'],
            'User_Password' => Hash::make($validated['User_Password']),
            'User_Role'     => $validated['User_Role'],
            'Status'        => $validated['Status'],
        ]);

        return response()->json([
            'success' => true,
            'data'    => new UserResource($user),
            'message' => 'User created successfully.',
        ], 201);
    }

    /** PUT /api/users/{id} — update a user; password optional. */
    public function update(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'User_Name'     => ['required', 'string', 'max:40', Rule::unique('users', 'User_Name')->ignore($user->User_ID, 'User_ID')],
            'User_Email'    => ['required', 'email', 'max:255', Rule::unique('users', 'User_Email')->ignore($user->User_ID, 'User_ID')],
            'User_Role'     => ['required', Rule::in($this->roles)],
            'Status'        => ['required', Rule::in(['Active', 'Inactive'])],
            'User_Password' => ['nullable', 'string', 'min:8'],
        ]);

        // Guard rail: an admin can't demote themselves out of Admin here,
        // which would otherwise lock user management away with no other
        // admin to undo it.
        if ((int) $user->User_ID === (int) $request->user()->User_ID && $validated['User_Role'] !== 'Admin') {
            return response()->json([
                'message' => 'You cannot remove your own Admin role.',
                'errors'  => ['User_Role' => ['You cannot remove your own Admin role.']],
            ], 422);
        }

        $user->User_Name  = $validated['User_Name'];
        $user->User_Email = $validated['User_Email'];
        $user->User_Role  = $validated['User_Role'];
        $user->Status     = $validated['Status'];

        if (! empty($validated['User_Password'])) {
            $user->User_Password = Hash::make($validated['User_Password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'data'    => new UserResource($user),
            'message' => 'User updated successfully.',
        ]);
    }

    /** DELETE /api/users/{id} — delete a user, with the web guard rails. */
    public function destroy(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);

        if ((int) $user->User_ID === (int) $request->user()->User_ID) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        if ($user->User_Role === 'Admin' && User::where('User_Role', 'Admin')->count() <= 1) {
            return response()->json([
                'message' => 'You cannot delete the last remaining Admin account.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}
