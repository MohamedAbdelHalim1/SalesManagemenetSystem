<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


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

    public function store(Request $request)
    {
        // Extract all users from the request
        $users = $request->all();
    
        // Ensure we can process an array of users
        foreach ($users as $userData) {
            // Validate each user's data
            $validator = validator($userData, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role_id' => 'required|exists:roles,id',
            ]);
    
            // Skip invalid users and continue with valid ones
            if ($validator->fails()) {
                continue; // Log or notify about invalid rows if needed
            }
    
            // Create user in the database
            $validated = $validator->validated();
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
            ]);
        }
    
        return response()->json(['success' => true, 'message' => 'Users imported successfully']);
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
