<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if the authenticated user has permission to view users
        if (!auth()->user() || !$this->hasPermission(auth()->user(), 'view_users')) {
            abort(403, 'Unauthorized to view users.');
        }

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $usersQuery = User::query();

        // Apply search filter if provided
        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if the authenticated user has permission to create users
        if (!auth()->user() || !$this->hasPermission(auth()->user(), 'create_users')) {
            abort(403, 'Unauthorized to create users.');
        }

        $validatedData = $this->validateUserData($request, false, null);

        // If validation fails, the method would have already returned an error response
        // So if we reach here, validation passed and $validatedData contains the validated data

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'permissions' => $validatedData['permissions'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user,
        ], 201);
    }

    /**
     * Validate user data.
     */
    protected function validateUserData(Request $request, bool $isUpdate = false, ?User $user = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'operator', 'manager'])],
            'permissions' => 'array',
            'permissions.*' => 'string',
        ];

        if ($isUpdate) {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['email'] = [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($user->id)
            ];
            $rules['password'] = 'sometimes|string|min:8|confirmed';
            $rules['role'] = ['sometimes', Rule::in(['admin', 'operator', 'manager'])];
            $rules['permissions'] = 'sometimes|array';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Throw a ValidationException which Laravel handles automatically
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Check if the authenticated user has permission to view users
        if (!$this->hasPermission(auth()->user(), 'view_users')) {
            abort(403, 'Unauthorized to view users.');
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Check if the authenticated user has permission to edit users
        if (!$this->hasPermission(auth()->user(), 'edit_users')) {
            abort(403, 'Unauthorized to edit users.');
        }

        $validatedData = $this->validateUserData($request, true, $user);

        $updateData = [];

        if (isset($validatedData['name'])) {
            $updateData['name'] = $validatedData['name'];
        }

        if (isset($validatedData['email'])) {
            $updateData['email'] = $validatedData['email'];
        }

        if (isset($validatedData['role'])) {
            $updateData['role'] = $validatedData['role'];
        }

        if (isset($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        if (isset($validatedData['permissions'])) {
            $updateData['permissions'] = $validatedData['permissions'];
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if the authenticated user has permission to delete users
        if (!$this->hasPermission(auth()->user(), 'delete_users')) {
            abort(403, 'Unauthorized to delete users.');
        }

        // Prevent deletion of the current user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Check if the user has the specified permission.
     */
    protected function hasPermission($user, $permission)
    {
        if (!$user) {
            return false;
        }

        // Admins have all permissions
        if ($user->role === 'admin') {
            return true;
        }

        // Check if the specific permission exists in the user's permissions array
        $userPermissions = $user->permissions ?? [];

        return in_array($permission, $userPermissions);
    }
}
