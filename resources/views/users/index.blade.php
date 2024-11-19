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
                        <button id="importUsersBtn" class="bg-gray-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" style="float:right;">
                            Import Users
                        </button>
                    </div>
                @endif


                <!-- Import Users Modal -->
                <div id="importUsersModal" class="hidden fixed inset-0">
                    <div class="modal-content">
                        <h2 class="text-lg font-bold mb-4">Import Users</h2>
                        <input type="file" id="importFile" accept=".csv" class="mb-4 w-full">
                        <div class="text-right">
                            <button id="importCancelBtn" class="bg-gray-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </button>
                            <button id="importSubmitBtn" class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Import
                            </button>
                        </div>
                    </div>
                </div>
                


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
        .hidden {
            display: none;
        }

        #importUsersModal {
            position: fixed;
            z-index: 1050;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Ensure the modal starts hidden */
            justify-content: center;
            align-items: center;
        }

        #importUsersModal .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 100%;
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
    <script>
     document.addEventListener('DOMContentLoaded', function () {
    const importUsersBtn = document.getElementById('importUsersBtn');
    const importUsersModal = document.getElementById('importUsersModal');
    const importCancelBtn = document.getElementById('importCancelBtn');
    const importSubmitBtn = document.getElementById('importSubmitBtn');
    const importFileInput = document.getElementById('importFile');

    // Function to show the modal
    function showModal() {
        importUsersModal.classList.remove('hidden');
        importUsersModal.style.display = 'flex';
    }

    // Function to hide the modal
    function hideModal() {
        importUsersModal.classList.add('hidden');
        importUsersModal.style.display = 'none';
        importFileInput.value = ''; // Clear the file input
    }

    // Show modal on button click
    importUsersBtn.addEventListener('click', showModal);

    // Hide modal on Cancel button click
    importCancelBtn.addEventListener('click', hideModal);

    // Handle file import on Import button click
    importSubmitBtn.addEventListener('click', () => {
        const file = importFileInput.files[0];
        if (!file) {
            alert('Please select a CSV file.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const csvData = e.target.result;
            const rows = csvData.split('\n');

            // Skip empty rows and ensure all columns exist
            const users = rows.slice(1).map(row => {
                const columns = row.split(',');
                if (columns.length < 4 || columns.some(col => col.trim() === '')) {
                    return null; // Skip invalid rows
                }
                const [name, email, password, role_id] = columns;
                return { name: name.trim(), email: email.trim(), password: password.trim(), role_id: role_id.trim() };
            }).filter(user => user !== null); // Remove null entries

            if (users.length === 0) {
                alert('No valid users found in the file.');
                return;
            }

            // Send data to the server
            fetch('{{ route('users.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(users),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Users imported successfully.');
                        window.location.reload();
                    } else {
                        alert('Error importing users: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while importing users.');
                });

            hideModal(); // Hide modal after processing
        };
        reader.readAsText(file);
    });

    // Close modal if clicking outside modal content
    importUsersModal.addEventListener('click', (event) => {
        if (event.target === importUsersModal) {
            hideModal();
        }
    });
});

    </script>
    
</x-app-layout>
