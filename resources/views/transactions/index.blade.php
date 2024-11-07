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
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('transaction.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Create Transaction</a>
                    </div>

                    <table id="transactionsTable" class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Open Date</th>
                                <th class="py-2 px-4 border-b">Close Date</th>
                                <th class="py-2 px-4 border-b">Transaction Count</th>
                                <th class="py-2 px-4 border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($openCloseRecords as $record)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->open_at }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->close_at ?? 'Open' }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $record->transactions->count() }}</td>
                                    <td class="py-2 px-4 border-b text-center" style="display: flex;">
                                        <a href="{{ route('transactions.show', $record->id) }}" class="btn-same-size">Show Details</a>
                                        @if(empty($record->close_at))
                                            <button onclick="openModal({{ $record->id }})" class="btn-same-size ml-2">
                                                Manage Transactions
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
    </div>

    <!-- Modal Structure -->
    <div id="transactionModal" class="modal hidden fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <!-- Modal Content -->
        <div class="bg-white rounded-lg w-1/2 p-6">
            <h2 class="text-xl font-semibold mb-4">Manage Transactions</h2>
            <ul id="transactionList"></ul>
            <button onclick="closeModal()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>

    <style>
        .btn-same-size {
            width: 120px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #6b7280; /* Gray color */
            color: white;
            border-radius: 4px;
            font-weight: bold;
        }
    
        .btn-same-size:hover {
            background-color: #4b5563; /* Slightly darker gray on hover */
        }
    </style>

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#transactionsTable').DataTable();
        });

        function openModal(recordId) {
            // Fetch transaction data for the record
            fetch(`/api/transactions/${recordId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate the modal with transaction data
                    const transactionList = document.getElementById('transactionList');
                    transactionList.innerHTML = ''; // Clear previous content
                    data.transactions.forEach(transaction => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('mb-2');
                        listItem.innerHTML = `<a href="/transactions/${transaction.id}/edit" class="text-blue-500 hover:underline">
                                                Edit Transaction #${transaction.id} - For ${transaction.user.name} - at ${new Date(transaction.created_at).toLocaleTimeString()}
                                              </a>`;
                        transactionList.appendChild(listItem);
                    });

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
