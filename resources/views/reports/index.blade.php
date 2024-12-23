<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">User Reports</h3>

                <!-- Date Range Filter -->
                <div class="flex gap-4 mb-4">
                    <div>
                        <label for="openDay" class="block font-semibold mb-1">Open Day</label>
                        <input type="date" id="openDay" class="border rounded px-3 py-2">
                    </div>
                    <div>
                        <label for="closeDay" class="block font-semibold mb-1">Close Day</label>
                        <input type="date" id="closeDay" class="border rounded px-3 py-2">
                    </div>
                    <div class="flex items-end gap-2">
                        <button id="filterBtn" class="filter-btn text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
                        <button id="resetBtn" class="reset-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Reset</button>
                    </div>
                </div>

                <!-- User Reports Table -->
                <table class="w-full border rounded" id="reportsTable">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4">Day Reference</th>
                            <th class="py-2 px-4">Opened At</th>
                            <th class="py-2 px-4">Closed At</th>
                            <th class="py-2 px-4">Total Cash</th>
                            <th class="py-2 px-4">Total Transfers</th>
                            <th class="py-2 px-4">Total Expenses</th>

                            <th class="py-2 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openCloses as $openClose)
                            @php
                                $total_transfer = 0;
                                $total_expenses = 0;
                                $total_cash = 0;
                                foreach ($openClose->transactions as $transaction){
                                    $total_cash += $transaction->total_remaining;
                                    foreach ($transaction->transfers as $transfer){
                                        $total_transfer += $transfer->transfer_value;                               
                                    }
                                    foreach ($transaction->expenses as $expenses){
                                        $total_expenses += $expenses->expenses_value;   

                                    }
                                }                                                           
                            @endphp
                            
                            <tr class="border-t report-row">
                                <td class="py-2 px-4 text-center">{{ $openClose->id }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $openClose->open_at }}</td>
                                <td class="py-2 px-4 text-center close-date">{{ $openClose->close_at ?? 'Open' }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $total_cash }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $total_transfer }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $total_expenses }}</td>

                                <td class="py-2 px-4 text-center" style="display: flex;">
                                    <a href="{{ route('reports.show', $openClose->id) }}" class="btn-same-size">
                                        Show
                                    </a>
                                    @if(Auth::user()->role_id == 1 && $openClose->close_at !== null)
                                        <!-- Reopen Button for Admins Only -->
                                        <button onclick="confirmReopen({{ $openClose->id }})" class="btn-same-size" style="margin-left: 10px;">
                                            Reopen
                                        </button>
                                    @endif
                                    @if(Auth::user()->role_id == 1)
                                        <!-- Delete Button -->
                                        <button onclick="confirmDelete({{ $openClose->id }})" class="btn-same-size bg-red-600 hover:bg-red-700" style="margin-left: 10px;">
                                            Delete
                                        </button>
                                    @endif
                                </td>                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .filter-btn{
            background-color: rgb(172, 90, 250);
        }
        .filter-btn, .reset-btn{
            width: 70px;
            height: 40px;
            margin-top: 15px;
        }
        .btn-same-size {
            width: 70px; /* Set a consistent width */
            height: 40px; /* Set a consistent height */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #6b7280; /* Gray color */
            color: white;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s; /* Smooth hover effect */
        }

        .btn-same-size:hover {
            background-color: #4b5563; /* Slightly darker gray on hover */
        }

    </style>
    <!-- JavaScript to Filter by Date Range -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#reportsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "columnDefs": [
                    { "searchable": true, "targets": [0] } // Disable searching for ID, Role, and Actions columns
                ],
            });
        });
        document.getElementById('filterBtn').addEventListener('click', function() {
            const openDay = document.getElementById('openDay').value;
            const closeDay = document.getElementById('closeDay').value;
            const rows = document.querySelectorAll('.report-row');

            rows.forEach(row => {
                const openDate = row.querySelector('.open-date').textContent.trim();
                const closeDate = row.querySelector('.close-date').textContent.trim();
                let showRow = true;

                if (openDay && new Date(openDate) < new Date(openDay)) {
                    showRow = false;
                }

                if (closeDay && closeDate !== 'Open' && new Date(closeDate) > new Date(closeDay)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            document.getElementById('openDay').value = '';
            document.getElementById('closeDay').value = '';
            const rows = document.querySelectorAll('.report-row');
            rows.forEach(row => {
                row.style.display = '';
            });
        });


        function confirmReopen(id) {
            console.log(id);
            
        if (confirm('Are you sure you want to reopen?')) {
            fetch(`/reports/${id}/reopen`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Reload the page or update the row to reflect the change
                    location.reload();
                } else {
                    alert('Failed to reopen the report.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }


    </script>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
            fetch(`/reports/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('Report deleted successfully.');
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Failed to delete the report.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>

</x-app-layout>
