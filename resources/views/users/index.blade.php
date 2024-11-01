<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (auth()->user()->role_id == 1)
                    <!-- Add New User Button -->
                    <div class="mb-4 text-right">
                        <a href="{{ route('users.create') }}"
                            class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New User
                        </a>
                    </div>
                @endif

                <!-- User Table -->
                <table id="usersTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Name</th>
                            <th class="py-2 px-4 border-b text-left">Email</th>
                            <th class="py-2 px-4 border-b text-left">Role</th>
                            @if(auth()->user()->role_id == 1)
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $user->id }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $user->name }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $user->email }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $user->role->name ?? 'N/A' }}</td>
                                @if(auth()->user()->role_id == 1)
                                    <td class="action-button  py-2 px-4 border-b text-center">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="edit text-yellow-600 font-bold py-1 px-2 rounded">Edit</a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete font-bold py-1 px-2 rounded"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .action-button {
            padding: 6px 12px;
            font-weight: bold;
            border-radius: 4px;
        }

        .action-button .edit {
            color: green;
            /* Darker yellow color */
        }

        .action-button .delete {
            color: red;
        }
    </style>



    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "pageLength": 10, // Set the default number of rows
                "ordering": true, // Enable sorting
                "searching": true, // Enable search filter
                "language": {
                    "lengthMenu": "Show _MENU_ entries per page",
                    "zeroRecords": "No users found",
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No users available",
                    "infoFiltered": "(filtered from _MAX_ total records)"
                }
            });
        });
    </script>
</x-app-layout>
