<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">Role Name</label>
                        <input type="text" name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <button type="submit" class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Save Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
