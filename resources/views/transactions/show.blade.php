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

                <div class="text-right mt-4" style="float:right;display:flex;">
                    <button onclick="printDiv('printableArea')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-700">Print Report</button>
                    @if (is_null($openClose->close_at))
                    <form method="POST" action="{{ route('transactions.closeDay', $openClose->id) }}" onsubmit="return confirm('Are you sure you want to close the day?')">
                        @csrf
                        <input type="hidden" name="total_cash" value="{{ $totalcashforclose }}">
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
                            <th class="py-2 px-4 border-b">Total Remaining</th>
                            <th class="py-2 px-4 border-b">Sales Commission</th>
                            <th class="py-2 px-4 border-b">Total Cash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalCashSum = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            @php $totalCashSum += $transaction->total_cash; @endphp
                            <tr>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->reference_collection }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->user->name }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->order_number }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->order_delivered }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->total_remaining }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->sales_commission }}</td>
                                <td class="py-2 px-4 border-b text-center">{{ $transaction->total_cash }}</td>
                            </tr>
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
                            <th class="py-2 px-4 border-b">Transfer Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalTransferValueSum = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->transfers as $transfer)
                                @php $totalTransferValueSum += $transfer->transfer_value; @endphp
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $transfer->transfer_key }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $transfer->transfer_value }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-2 px-4 text-right font-bold">Total Transfer Value:</td>
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
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->expenses as $expense)
                                @php $totalExpenseValueSum += $expense->expenses_value; @endphp
                                <tr>
                                    <td class="py-2 px-4 border-b text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $expense->expenses_key }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $expense->expenses_value }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-2 px-4 text-right font-bold">Total Expense Value:</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $totalExpenseValueSum }}</td>
                        </tr>
                    </tfoot>
                </table>

        <!-- Coins Table -->
        <h5 class="font-semibold mt-6 mb-4">Coins</h5>
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">0.5</th>
                    <th class="py-2 px-4 border-b">1</th>
                    <th class="py-2 px-4 border-b">10</th>
                    <th class="py-2 px-4 border-b">20</th>
                    <th class="py-2 px-4 border-b">50</th>
                    <th class="py-2 px-4 border-b">100</th>
                    <th class="py-2 px-4 border-b">200</th>
                    <th class="py-2 px-4 border-b">Money Shortage</th>
                </tr>
            </thead>
            <tbody>

                @if($openClose->coin)
                    <tr>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_0_5 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_1 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_10 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_20 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_50 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_100 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->coin_200 }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $openClose->coin->money_shortage }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="8" class="py-2 px-4 border-b text-center">No currency added yet</td>
                    </tr>
                @endif
            </tbody>
        </table>



                <!-- Final Calculation -->
                <div class="mt-8 text-right">
                    <h4 class="font-semibold text-xl">Final Cash Calculation:</h4>
                    <p class="font-bold">{{ $totalCashSum - ($totalTransferValueSum + $totalExpenseValueSum) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS and JS for Styling and Interactive Features -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables for all tables with search, pagination, and length-change disabled
            $('#transactionsTable').DataTable({
                searching: false,
                paging: false,
                info: false
            });
            $('#transfersTable').DataTable({
                searching: false,
                paging: false,
                info: false
            });
            $('#expensesTable').DataTable({
                searching: false,
                paging: false,
                info: false
            });
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
