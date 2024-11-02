<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" id="printableArea">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- OpenClose Information -->
                <h3 class="font-semibold text-lg mb-4">Transaction Information</h3>
                <!-- Print Button -->
                <div class="text-right mt-4" style="float:right;">
                    <button onclick="printDiv('printableArea')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-700">Print Report</button>
                </div>

                <p>Open Date: {{ $openClose->open_at }}</p>
                <p>Close Date: {{ $openClose->close_at ?? 'Open' }}</p>
                @if($openClose->close_at != null)
                    <p>Closed By: <b>{{ $openClose->user->name }}</b></p>
                @endif

                <!-- Transactions Table -->
                <h3 class="font-semibold text-lg mt-6 mb-4">Transactions</h3>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Reference Collection</th>
                            <th class="py-2 px-4 border-b">Sales Name</th>
                            <th class="py-2 px-4 border-b">Order Number</th>
                            <th class="py-2 px-4 border-b">Orders Delivered</th>
                            <th class="py-2 px-4 border-b">Total Remaining</th>
                            <th class="py-2 px-4 border-b">Sales Commission</th>
                            <th class="py-2 px-4 border-b">Total Cash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalCash = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            <tr class="border-t">
                                <td class="py-2 px-4 text-center">{{ $transaction->id }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->reference_collection }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->user->name }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->order_number }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->order_delivered }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->total_remaining }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->sales_commission }}</td>
                                <td class="py-2 px-4 text-center">{{ $transaction->total_cash }}</td>
                            </tr>
                            @php $totalCash += $transaction->total_cash; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100">
                            <td colspan="7" class="py-2 px-4 text-right font-semibold">Total Cash:</td>
                            <td class="py-2 px-4 text-center font-semibold">{{ $totalCash }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Transfers Table -->
                <h5 class="font-semibold mt-6 mb-4">Transfers</h5>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Sales</th>
                            <th class="py-2 px-4 border-b">Transfer Key</th>
                            <th class="py-2 px-4 border-b">Transfer Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalTransfers = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->transfers as $transfer)
                                <tr class="border-t">
                                    <td class="py-2 px-4 text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 text-center">{{ $transaction->user->name }}</td>
                                    <td class="py-2 px-4 text-center">{{ $transfer->transfer_key }}</td>
                                    <td class="py-2 px-4 text-center">{{ $transfer->transfer_value }}</td>
                                </tr>
                                @php $totalTransfers += $transfer->transfer_value; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100">
                            <td colspan="3" class="py-2 px-4 text-right font-semibold">Total Transfers:</td>
                            <td class="py-2 px-4 text-center font-semibold">{{ $totalTransfers }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Expenses Table -->
                <h5 class="font-semibold mt-6 mb-4">Expenses</h5>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Transaction ID</th>
                            <th class="py-2 px-4 border-b">Sales</th>
                            <th class="py-2 px-4 border-b">Expense Key</th>
                            <th class="py-2 px-4 border-b">Expense Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalExpenses = 0; @endphp
                        @foreach($openClose->transactions as $transaction)
                            @foreach($transaction->expenses as $expense)
                                <tr class="border-t">
                                    <td class="py-2 px-4 text-center">{{ $transaction->id }}</td>
                                    <td class="py-2 px-4 text-center">{{ $transaction->user->name }}</td>
                                    <td class="py-2 px-4 text-center">{{ $expense->expenses_key }}</td>
                                    <td class="py-2 px-4 text-center">{{ $expense->expenses_value }}</td>
                                </tr>
                                @php $totalExpenses += $expense->expenses_value; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100">
                            <td colspan="3" class="py-2 px-4 text-right font-semibold">Total Expenses:</td>
                            <td class="py-2 px-4 text-center font-semibold">{{ $totalExpenses }}</td>
                        </tr>
                    </tfoot>
                </table>
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
                    <h4 class="text-xl font-semibold">Final Calculation</h4>
                    <p class="text-lg">
                        <strong>{{ $totalCash - ($totalTransfers + $totalExpenses) }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    
    <!-- JavaScript for Printing -->
    <script>
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
