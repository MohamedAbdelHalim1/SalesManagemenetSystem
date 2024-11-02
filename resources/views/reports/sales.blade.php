<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">Salesperson: <b>{{ $salesUser->name }}</b></h3>
                
                <!-- Date Range Filter -->
                <div class="mb-4 flex gap-4">
                    <div>
                        <label for="startDate" class="block font-semibold mb-2">Start Date:</label>
                        <input type="date" id="startDate" class="form-input border rounded px-3 py-2">
                    </div>
                    <div>
                        <label for="endDate" class="block font-semibold mb-2">End Date:</label>
                        <input type="date" id="endDate" class="form-input border rounded px-3 py-2">
                    </div>
                </div>
                
                <!-- Filter and Reset Buttons -->
                <div class="mb-4 flex gap-4">
                    <button id="filterBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-600" style="height: 40px;">Filter</button>
                    <button id="resetBtn" class="reset-btn text-white px-4 py-2 rounded hover:bg-red-600" style="height: 40px;">Reset</button>
                </div>

                <!-- Sales Report Table -->
                <table class="w-full border rounded datatable">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4">Created At</th>
                            <th class="py-2 px-4">Reference Collection</th>
                            <th class="py-2 px-4">Order Number</th>
                            <th class="py-2 px-4">Orders Delivered</th>
                            <th class="py-2 px-4">Total Remaining</th>
                            <th class="py-2 px-4">Sales Commission</th>
                            <th class="py-2 px-4">Total Cash</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="py-2 px-4 text-center">{{ $transaction->created_at }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->reference_collection }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->order_number }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->order_delivered }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->total_remaining }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->sales_commission }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->total_cash }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include jQuery and DataTables JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <style>
        .reset-btn{
            background-color: red;
        }
    </style>
    <!-- Initialize Date Filtering and DataTables -->
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            const table = $('.datatable').DataTable();

            // Filter button click event
            $('#filterBtn').on('click', function() {
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                // Clear any previous filters
                $.fn.dataTable.ext.search = [];

                // Apply the date filter only if both dates are selected
                if (startDate && endDate) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        const createdAt = data[0]; // "Created At" column index (0-based)
                        return (createdAt >= startDate && createdAt <= endDate);
                    });
                }

                // Redraw the table with the new filter
                table.draw();
            });

            // Reset button click event
            $('#resetBtn').on('click', function() {
                // Clear date inputs and remove filters
                $('#startDate').val('');
                $('#endDate').val('');
                $.fn.dataTable.ext.search = []; // Clear all filters
                table.draw(); // Redraw the table
            });
        });
    </script>
</x-app-layout>
