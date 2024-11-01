<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name, Email, Password, Role -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="block w-full border-gray-300 rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="block w-full border-gray-300 rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Password (leave blank to keep current password)</label>
                        <input type="password" name="password" class="block w-full border-gray-300 rounded-md">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Role</label>
                        <select name="role_id" class="block w-full border-gray-300 rounded-md" required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update User
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
