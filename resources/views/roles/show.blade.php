<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $role->name }}'s Role Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                                
                <!-- Users Table -->
                <h4 class="text-xl font-semibold mt-8 mb-4">Users with this Role</h4>

                @if($role->users->isEmpty())
                    <p>No users are assigned to this role.</p>
                @else
                    <table id="usersTable" class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">User ID</th>
                                <th class="py-2 px-4 border-b text-left">Name</th>
                                <th class="py-2 px-4 border-b text-left">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $user->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $user->name }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $user->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "columnDefs": [
                    { "searchable": false, "targets": [0] } // Disable searching for ID, Role, and Actions columns
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
