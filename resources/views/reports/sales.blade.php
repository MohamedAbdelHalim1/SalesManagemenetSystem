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
                        @php
                            $totalRemainingSum = 0;
                            $salesCommissionSum = 0;
                            $totalCashSum = 0;
                        @endphp
                        @foreach($transactions as $transaction)
                            @php
                                $totalRemainingSum += $transaction->total_remaining;
                                $salesCommissionSum += $transaction->sales_commission;
                                $totalCashSum += $transaction->total_cash;
                            @endphp
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
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="5" class="py-2 px-4 text-right font-bold">Totals:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $salesCommissionSum }}</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalCashSum }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Transfers Table -->
                <h5 class="font-semibold mt-6 mb-4">Transfers</h5>
                <table class="w-full border rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4">Transaction ID</th>
                            <th class="py-2 px-4">Transfer Key</th>
                            <th class="py-2 px-4">Transfer Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalTransferValueSum = 0; @endphp
                        @foreach($transactions as $transaction)
                            @foreach($transaction->transfers as $transfer)
                                @php $totalTransferValueSum += $transfer->transfer_value; @endphp
                                <tr>
                                    <td class="py-2 px-4 text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 text-center">{{ $transfer->transfer_key }}</td>
                                    <td class="py-2 px-4 text-center">{{ $transfer->transfer_value }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="2" class="py-2 px-4 text-right font-bold">Total Transfer Value:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalTransferValueSum }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Expenses Table -->
                <h5 class="font-semibold mt-6 mb-4">Expenses</h5>
                <table class="w-full border rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4">Transaction ID</th>
                            <th class="py-2 px-4">Expense Key</th>
                            <th class="py-2 px-4">Expense Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalExpenseValueSum = 0; @endphp
                        @foreach($transactions as $transaction)
                            @foreach($transaction->expenses as $expense)
                                @php $totalExpenseValueSum += $expense->expenses_value; @endphp
                                <tr>
                                    <td class="py-2 px-4 text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 text-center">{{ $expense->expenses_key }}</td>
                                    <td class="py-2 px-4 text-center">{{ $expense->expenses_value }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="2" class="py-2 px-4 text-right font-bold">Total Expense Value:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalExpenseValueSum }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Include jQuery and DataTables JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('.datatable').DataTable({
                searching: false,
                paging: false,
                info: false
            });
        });
    </script>
</x-app-layout>
