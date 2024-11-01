<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Name, Email, Password, Role -->
                    <div class="mb-4">
                        <label class="block text-gray-700">Name</label>
                        <input type="text" name="name" class="block w-full border-gray-300 rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" class="block w-full border-gray-300 rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Password</label>
                        <input type="password" name="password" class="block w-full border-gray-300 rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Role</label>
                        <select name="role_id" class="block w-full border-gray-300 rounded-md" required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create User
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
