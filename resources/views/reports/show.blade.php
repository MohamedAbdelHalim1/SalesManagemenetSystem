<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" id="printableArea">

                <!-- OpenClose Information -->
                <h3 class="font-semibold text-lg mb-4">Transaction Information</h3>

                <div class="text-right mt-4" style="float:right; display:flex;">
                    <button onclick="printDiv('printableArea')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-700">Print Report</button>
                    @if (is_null($openClose->close_at))
                    <form method="POST" action="{{ route('transactions.closeDay', $openClose->id) }}" onsubmit="return confirm('Are you sure you want to close the day?')">
                        @csrf
                        <input type="hidden" id="total_cash" name="total_cash" value="">
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded" style="float:right;">Close Day</button>
                    </form>                    
                    @endif
                </div>

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
                            <th class="py-2 px-4 border-b">Sales Commission</th>
                            <th class="py-2 px-4 border-b">Total Remaining</th>
                            <th class="py-2 px-4 border-b">Total After Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalCashSum = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            @php $calculatedCash = $transaction->total_remaining - $transaction->sales_commission; @endphp
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->reference_collection }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->user->name }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->order_number }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->order_delivered }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->sales_commission }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->total_remaining }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $calculatedCash }}</td>
                            </tr>
                            @php $totalCashSum += $calculatedCash; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="py-2 px-4 text-right font-bold">Total Cash:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalCashSum }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Transfers Table -->
                <h5 class="font-semibold mt-6 mb-4">Transfers</h5>
                <table id="transfersTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Transfer Key</th>
                            <th class="py-2 px-4 border-b">Transfer Image</th>
                            <th class="py-2 px-4 border-b">Transfer Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalTransferValueSum = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->transfers as $transfer)
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $transfer->transfer_key }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        @if($transfer->image)
                                            <a href="{{ asset($transfer->image) }}" target="_blank">
                                                <img src="{{ asset($transfer->image) }}" alt="Transfer Image" class="w-20 h-20 object-cover" />
                                            </a>
                                        @else
                                            <span>No image attached</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">{{ $transfer->transfer_value }}</td>
                                </tr>
                                @php $totalTransferValueSum += $transfer->transfer_value; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-2 px-4 text-right font-bold">Total Transfer Value:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalTransferValueSum }}</td>
                        </tr>
                    </tfoot>
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
                        @php $totalExpenseValueSum = 0; @endphp
                        @foreach($generalExpenses as $expense)
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $expense->transaction_id }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $expense->expenses_key }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $expense->expenses_value }}</td>
                            </tr>
                            @php $totalExpenseValueSum += $expense->expenses_value; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-2 px-4 text-right font-bold">Total Expenses:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalExpenseValueSum }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Final Calculation -->
                <div class="mt-8 text-right" style="float:right;">
                    <h4 class="font-semibold text-xl">Final Cash Calculation:</h4>
                    <p class="font-bold">{{ $totalCashSum - ($totalTransferValueSum + $totalExpenseValueSum) }} LE</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#transactionsTable, #transfersTable, #expensesTable').DataTable({
                searching: false,
                paging: false,
                info: false
            });

            // Set hidden input value for total_cash
            const totalCashSum = {{ $totalCashSum }};
            const totalTransferValueSum = {{ $totalTransferValueSum }};
            const totalExpenseValueSum = {{ $totalExpenseValueSum }};
            const finalCash = totalCashSum - (totalTransferValueSum + totalExpenseValueSum);
            $('#total_cash').val(finalCash);
        });

        function printDiv(divId) {
            const content = document.getElementById(divId).innerHTML;
            const originalContent = document.body.innerHTML;
            document.body.innerHTML = content;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    </script>
</x-app-layout>
