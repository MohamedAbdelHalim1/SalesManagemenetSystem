<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions Review') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Transactions Table -->
                    <table id="transactionsTable" class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Day Reference</th>
                                <th class="py-2 px-4 border-b">Open Date</th>
                                <th class="py-2 px-4 border-b">Close Date</th>
                                <th class="py-2 px-4 border-b">Transaction Count</th>
                                <th class="py-2 px-4 border-b">Actions</th>
                                <th class="py-2 px-4 border-b">All is Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($openCloseRecords as $record)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->open_at }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->close_at ?? 'Open' }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->transactions->count() }}</td>
                                    <td class="py-2 px-4 border-b text-center flex">
                                        <a href="{{ route('reports.show', $record->id) }}" class="btn-same-size" style="margin-right: 5px;">Show Details</a>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <input type="checkbox" 
                                               class="is-done-checkbox" 
                                               data-id="{{ $record->id }}" 
                                               {{ $record->is_done ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-same-size {
            width: 120px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #6b7280;
            color: white;
            border-radius: 4px;
            font-weight: bold;
        }

        .btn-same-size:hover {
            background-color: #4b5563;
        }

        .modal.hidden {
            display: none;
        }
    </style>

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#transactionsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "columnDefs": [
                    { "searchable": true, "targets": [0] } // Disable searching for ID, Role, and Actions columns
                ],
            });

            // Handle the checkbox change event
            $('.is-done-checkbox').on('change', function () {
                const recordId = $(this).data('id');
                const isDone = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: `/open-closes/${recordId}/update-done`, // Define your route
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_done: isDone
                    },
                    success: function (response) {
                        alert(response.message);
                    },
                    error: function () {
                        alert('An error occurred while updating the record.');
                    }
                });
            });
        });
    </script>
</x-app-layout>
