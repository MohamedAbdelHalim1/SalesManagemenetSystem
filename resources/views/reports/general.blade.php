<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('General Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">General Reports</h3>

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

                <!-- Transaction Modal -->
                <div id="transactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-2/3">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Transaction Details</h2>
                            <button id="closeModalBtn" class="text-red-600 font-bold text-lg">&times;</button>
                        </div>
                        <table class="w-full border rounded">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4">User Name</th>
                                    <th class="py-2 px-4">Email</th>
                                    <th class="py-2 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionUsers"></tbody>
                        </table>
                    </div>
                </div>

                <!-- User Reports Table -->
                <table class="w-full border rounded" id="reportsTable">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4">Day Reference</th>
                            <th class="py-2 px-4">Opened At</th>
                            <th class="py-2 px-4">Closed At</th>
                            <th class="py-2 px-4">Accountant</th>
                            <th class="py-2 px-4">Sales</th>
                            <th class="py-2 px-4">Total Cash</th>
                            <th class="py-2 px-4">Total Transfers</th>
                            <th class="py-2 px-4">Total Expenses</th>
                            <th class="py-2 px-4">Is Reviwed</th>
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
                                    $total_cash += $transaction->total_cash;
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
                                <td class="py-2 px-4 text-center open-date">{{ $openClose->user->name }}</td>
                                <td class="py-2 px-4 text-center open-date"><a href="#" class="text-blue-500 transaction-count" data-transactions='@json($openClose->transactions)'>
                                    {{ $openClose->transactions->count() }}
                                </a></td>
                                <td class="py-2 px-4 text-center open-date">{{ $total_cash }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $total_transfer }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $total_expenses }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $openClose->is_done ? 'Yes' : 'No' }}</td>
                                <td class="py-2 px-4 text-center" style="display: flex;">
                                    <a href="{{ route('reports.show', $openClose->id) }}" class="btn-same-size">
                                        Show
                                    </a>
                                </td>                                
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-bold">
                            <td colspan="5" class="py-2 px-4 text-center">Total</td>
                            <td id="totalCash" class="py-2 px-4 text-center"></td>
                            <td id="totalTransfers" class="py-2 px-4 text-center"></td>
                            <td id="totalExpenses" class="py-2 px-4 text-center"></td>
                            <td class="py-2 px-4"></td>
                        </tr>
                    </tfoot>
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

        .modal {
            z-index: 1050; /* Bootstrap default value */
            position: fixed;
        }

        .modal-backdrop {
            z-index: 1040; /* Bootstrap default value */
        }
        #transactionModal {
            z-index: 9999; /* Ensure it appears above other elements */
            position: fixed;
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

            // Function to calculate totals in the footer
            function calculateTotals() {
                let totalCash = 0;
                let totalTransfers = 0;
                let totalExpenses = 0;

                // Loop through each visible row in the DataTable
                $('#reportsTable tbody tr').each(function() {
                    if ($(this).css('display') !== 'none') { // Only consider visible rows
                        totalCash += parseFloat($(this).find('td:nth-child(6)').text().trim()) || 0;
                        totalTransfers += parseFloat($(this).find('td:nth-child(7)').text().trim()) || 0;
                        totalExpenses += parseFloat($(this).find('td:nth-child(8)').text().trim()) || 0;
                    }
                });

                $('#totalCash').text(totalCash);
                $('#totalTransfers').text(totalTransfers);
                $('#totalExpenses').text(totalExpenses);
            }

            // Calculate totals initially
            calculateTotals();

            // Filter button event
            document.getElementById('filterBtn').addEventListener('click', function() {
                const openDay = document.getElementById('openDay').value;
                const closeDay = document.getElementById('closeDay').value;
                const rows = document.querySelectorAll('.report-row');

                rows.forEach(row => {
                    const openDate = row.querySelector('.open-date').textContent.trim();
                    const closeDate = row.querySelector('.close-date').textContent.trim();
                    let showRow = true;

                    // Filter logic based on selected dates
                    if (openDay && new Date(openDate) < new Date(openDay)) {
                        showRow = false;
                    }

                    if (closeDay && closeDate !== 'Open' && new Date(closeDate) > new Date(closeDay)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none'; // Show or hide the row
                });

                calculateTotals(); // Recalculate totals after filtering
            });

            // Reset button event
            document.getElementById('resetBtn').addEventListener('click', function() {
                document.getElementById('openDay').value = '';
                document.getElementById('closeDay').value = '';
                const rows = document.querySelectorAll('.report-row');
                rows.forEach(row => {
                    row.style.display = ''; // Show all rows
                });
                calculateTotals(); // Recalculate totals after reset
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
        document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('transactionModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const transactionUsers = document.getElementById('transactionUsers');

    // Open modal when transaction count is clicked
    document.querySelectorAll('.transaction-count').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const transactions = JSON.parse(this.dataset.transactions);
            //console.log(transactions);

            // Clear old data
            transactionUsers.innerHTML = '';

            // Populate the modal with user data
            transactions.forEach(transaction => {
                //console.log(transaction.user);
                const userName = transaction.user ? transaction.user.name : 'N/A';
                const userEmail = transaction.user ? transaction.user.email : 'N/A';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-2 px-4">${userName}</td>
                    <td class="py-2 px-4">${userEmail}</td>
                    <td class="py-2 px-4" style="display: flex; justify-content: center;margin:10px;">
                        <a href="/general-report/transactions/${transaction.id}" class="btn-same-size" style="margin-right: 5px; font-size: 12px;">Transactions</a>
                        <a href="/general-report/transfers/${transaction.id}" class="btn-same-size" style="margin-right: 5px; font-size: 12px;">Transfers</a>
                        <a href="/general-report/expenses/${transaction.id}" class="btn-same-size" style="margin-right: 5px; font-size: 12px;">Expenses</a>
                    </td>
                `;
                transactionUsers.appendChild(row);
            });


            // Show modal
            modal.classList.remove('hidden');
        });
    });

    // Close modal
    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });
});

    </script>
</x-app-layout>
