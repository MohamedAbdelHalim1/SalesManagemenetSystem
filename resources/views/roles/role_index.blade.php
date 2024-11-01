<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles Management') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Add New Role Button, floated to the right -->
                @if(auth()->user()->role_id == 1)
                <div class="mb-4 text-right">
                    <a href="{{ route('roles.create') }}" class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Role
                    </a>
                </div>
                @endif

                <table id="rolesTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Name</th>
                            @if(auth()->user()->role_id == 1)
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $role->id }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $role->name }}</td>
                                @if(auth()->user()->role_id == 1)
                                <td class="action-button py-2 px-4 border-b text-center">
                                    <a href="{{ route('roles.show', $role->id) }}" class="show btn">Show</a> |
                                    <a href="{{ route('roles.edit', $role->id) }}" class="edit btn">Edit</a>
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
        .action-button .show {
            color: navy; /* Darker yellow color */
        }
        .action-button .edit {
            color: green; /* Darker yellow color */
        }
        .action-button .delete{
            color:red;
        }
    </style>

     <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables script -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "columnDefs": [
                    { "searchable": false, "targets": [0 , 2] } // Disable searching for ID, Role, and Actions columns
                ],
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
