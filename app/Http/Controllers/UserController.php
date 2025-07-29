<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'department' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Tạo user
        $user = User::create([
            'name' => $validatedData['name'],
            'department' => $validatedData['department'],
            'password' => Hash::make($validatedData['password']),
            'is_admin' => $validatedData['role_id'] == 1 ? true : false, // Admin role có id = 1
        ]);

        // Gán role cho user (tự động tạo dữ liệu trong bảng model_has_roles)
        $role = Role::find($validatedData['role_id']);
        $user->assignRole($role);

        return redirect()->route('users.index')
            ->with('success', 'User đã được tạo thành công!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Cập nhật thông tin user
        $updateData = [
            'name' => $validatedData['name'],
            'department' => $validatedData['department'],
            'is_admin' => $validatedData['role_id'] == 1 ? true : false, // Admin role có id = 1
        ];

        // Chỉ cập nhật password nếu có nhập mới
        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($updateData);

        // Cập nhật role (xóa role cũ và gán role mới)
        $user->syncRoles([]);
        $role = Role::find($validatedData['role_id']);
        $user->assignRole($role);

        return redirect()->route('users.index')
            ->with('success', 'User đã được cập nhật thành công!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Không cho phép xóa chính mình
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không thể xóa chính mình!');
        }

        // Xóa user (roles sẽ tự động xóa do cascade)
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User đã được xóa thành công!');
    }
}
