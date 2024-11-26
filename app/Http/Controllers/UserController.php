<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function users(){
        $users = User::with('role')->get();
        return view('users.index',compact('users'));
    }
    public function roles(){
        $roles = Role::all();
        return view('roles.role_index', compact('roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }


    public function storeSingle(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    public function storeBulk(Request $request)
{
    $validator = Validator::make($request->all(), [
        'users' => 'required|array',
        'users.*.name' => 'required|string|max:255',
        'users.*.email' => 'required|string|email|max:255|unique:users,email',
        'users.*.password' => 'required|string|min:8',
        'users.*.role_id' => 'required|integer|exists:roles,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => $validator->errors()], 422);
    }

    foreach ($request->users as $userData) {
        User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role_id' => $userData['role_id'],
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Users imported successfully.']);
}





    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = $request->only('name', 'email', 'role_id');
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully.');
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('query');
        $users = User::where('name', 'like', '%' . $query . '%')->get();
        return response()->json($users);
    }


    public function create_role()
    {
        return view('roles.create');
    }

    public function store_role(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles']);
        Role::create($request->only('name'));

        return redirect()->route('roles')->with('success', 'Role created successfully.');
    }

    public function show_role(Role $role)
    {
        $role->load('users');  //load users of specific role , 'with' will load all roles
        return view('roles.show', compact('role'));
    }

    public function edit_role(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update_role(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);
        $role->update($request->only('name'));

        return redirect()->route('roles')->with('success', 'Role updated successfully.');
    }

    public function destroy_role(Role $role)
    {
        $role->delete();
        return redirect()->route('roles')->with('success', 'Role deleted successfully.');
    }



}
