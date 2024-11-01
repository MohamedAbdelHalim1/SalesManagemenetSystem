<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- OpenClose Information -->
                <h3 class="font-semibold text-lg mb-4">Transaction Information</h3>

                @if ( is_null($openClose->close_at) )
                    <form method="POST" action="{{ route('transactions.closeDay' ,$openClose->id) }}" onsubmit="return confirm('Are you sure you want to close the day?')">
                        @csrf
                        <input type="hidden" name="total_cash" value="{{ $totalcashforclose }}">
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded" style="float:right;">Close Day</button>
                    </form>

                @endif
                
                <p>Open Date: {{ $openClose->open_at }}</p>
                <p>Close Date: {{ $openClose->close_at ?? 'Open' }}</p>

                <!-- Transactions Table -->
                <h3 class="font-semibold text-lg mt-6 mb-4">Transactions</h3>
                <table id="transactionsTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Reference Collection</th>
                            <th class="py-2 px-4 border-b">Sales</th>
                            <th class="py-2 px-4 border-b">Order Number</th>
                            <th class="py-2 px-4 border-b">Orders Delivered</th>
                            <th class="py-2 px-4 border-b">Total Remaining</th>
                            <th class="py-2 px-4 border-b">Sales Commission</th>
                            <th class="py-2 px-4 border-b">Total Remaining</th>
                            <th class="py-2 px-4 border-b">Total Cash</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openClose->transactions as $transaction)
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->reference_collection }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->user->name }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->order_number }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->order_delivered }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->total_remaining }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->sales_commission }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->total_remaining }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->total_cash }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Transfers Table -->
                <h5 class="font-semibold mt-6 mb-4">Transfers</h5>
                <table id="transfersTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Transfer Key</th>
                            <th class="py-2 px-4 border-b">Transfer Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->transfers as $transfer)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $transfer->transfer_key }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $transfer->transfer_value }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

                <!-- Expenses Table -->
                <h5 class="font-semibold mt-6 mb-4">Expenses</h5>
                <table id="expensesTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Expense Key</th>
                            <th class="py-2 px-4 border-b">Expense Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->expenses as $expense)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $expense->expenses_key }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $expense->expenses_value }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

                
                
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS and JS for Styling and Interactive Features -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables for all tables
            $('#transactionsTable').DataTable();
            $('#transfersTable').DataTable();
            $('#expensesTable').DataTable();
        });
    </script>

</x-app-layout>
