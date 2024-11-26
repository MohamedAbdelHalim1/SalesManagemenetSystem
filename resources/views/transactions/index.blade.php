<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Action Buttons -->
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('transaction.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded mr-2" style="margin-right: 5px;">Create Transaction</a>
                        <a href="{{ route('expenses.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Add My Expenses</a>
                    </div>

                    <!-- Transactions Table -->
                    <table id="transactionsTable" class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Day Reference</th>
                                <th class="py-2 px-4 border-b">Open Date</th>
                                <th class="py-2 px-4 border-b">Close Date</th>
                                <th class="py-2 px-4 border-b">Transaction Count</th>
                                <th class="py-2 px-4 border-b">Actions</th>
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
                                        <a href="{{ route('transactions.show', $record->id) }}" class="btn-same-size" style="margin-right: 5px;">Show Details</a>
                                        @if(empty($record->close_at))
                                            <button onclick="openModal({{ $record->id }})" class="btn-same-size ml-2">Manage Transactions</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="transactionModal" class="modal hidden fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg w-1/2 p-6">
            <h2 class="text-xl font-semibold mb-4">Manage Transactions</h2>
            <ul id="transactionList"></ul>
            <button onclick="closeModal()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Close</button>
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
        });

        function openModal(recordId) {
            fetch(`/api/transactions/${recordId}`)
                .then(response => response.json())
                .then(data => {
                    const transactionList = document.getElementById('transactionList');
                    transactionList.innerHTML = '';

                    // Filter out transactions where user's role_id is 2
                    const filteredTransactions = data.transactions.filter(transaction => transaction.user.role_id !== 2);

                    if (filteredTransactions.length === 0) {
                        transactionList.innerHTML = '<p class="text-red-500">No transactions to display.</p>';
                    } else {
                        filteredTransactions.forEach(transaction => {
                            const listItem = document.createElement('li');
                            listItem.classList.add('mb-2');
                            listItem.innerHTML = `
                                <a href="/transactions/${transaction.id}/edit" class="text-blue-500 hover:underline">
                                    Edit Transaction #${transaction.id} - For ${transaction.user.name}
                                </a>`;
                            transactionList.appendChild(listItem);
                        });
                    }

                    // Show the modal
                    document.getElementById('transactionModal').classList.remove('hidden');
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
