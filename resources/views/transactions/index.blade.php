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
                                    <td class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('transactions.show', $record->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded">Show Details</a>
                                        @if(empty($record->close_at))
                                        <!-- Button to Open Modal -->
                                        <button onclick="openModal({{ $record->id }})" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">
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

    <!-- Modal Background -->
    <div id="modal-{{ $record->id }}" class="modal hidden fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <!-- Modal Content -->
        <div class="bg-white rounded-lg w-1/2 p-6">
            <h2 class="text-xl font-semibold mb-4">Manage Transactions</h2>
            <ul>
                @foreach($record->transactions as $transaction)
                    <li class="mb-2">
                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="text-blue-500 hover:underline">
                            Edit Transaction #{{ $transaction->id }} - For {{ $transaction->user->name }} - at {{ $transaction->created_at->format('g:i A') }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <button onclick="closeModal({{ $record->id }})" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>


        <!-- Include DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

        <!-- Include jQuery and DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#transactionsTable').DataTable();
        });

        function openModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
        }
    </script>
</x-app-layout>
