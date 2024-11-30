<x-app-layout>
    <div class="container mx-auto mt-8" style="padding: 50px;">
        <h2 class="text-2xl font-semibold mb-6">Special Report</h2>

        <!-- Form to Submit Filters -->
        <form method="GET" action="{{ route('special.report') }}">
            <div class="flex flex-wrap gap-6 mb-6">
                <div class="w-full sm:w-1/4">
                    <label for="openDay" class="block font-semibold mb-1">Open Day</label>
                    <input type="date" id="openDay" name="openDay" value="{{ request('openDay') }}" class="border rounded px-3 py-2 w-full">
                </div>
                <div class="w-full sm:w-1/4">
                    <label for="closeDay" class="block font-semibold mb-1">Close Day</label>
                    <input type="date" id="closeDay" name="closeDay" value="{{ request('closeDay') }}" class="border rounded px-3 py-2 w-full">
                </div>
                <div class="w-full sm:w-1/4">
                    <label for="accountant" class="block font-semibold mb-1">Accountant</label>
                    <select id="accountant" name="accountant" class="border rounded px-3 py-2 w-full">
                        <option value="">Select Accountant</option>
                        @foreach($accountants as $accountant)
                            <option value="{{ $accountant->id }}" {{ request('accountant') == $accountant->id ? 'selected' : '' }}>{{ $accountant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-1/4">
                    <label for="sales" class="block font-semibold mb-1">Sales</label>
                    <select id="sales" name="sales" class="border rounded px-3 py-2 w-full">
                        <option value="">Select Sales</option>
                        @foreach($sales as $sale)
                            <option value="{{ $sale->id }}" {{ request('sales') == $sale->id ? 'selected' : '' }}>{{ $sale->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="w-full sm:w-auto flex items-end gap-4">
                    <button type="submit" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Filter</button>
                    <button type="reset" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-500 transition" onclick="window.location='{{ route('special.report') }}'">Reset</button>

                </div>
            </div>
        </form>

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table id="transactionsTable" class="w-full table-auto border rounded shadow-md">
                <thead class="bg-gray-100">
                    <tr style="background-color: rgb(85, 85, 158);">
                        <th class="py-2 px-4 text-left text-white">Day Reference</th>
                        <th class="py-2 px-4 text-left text-white">Open At</th>
                        <th class="py-2 px-4 text-left text-white">Closed At</th>
                        <th class="py-2 px-4 text-left text-white">Transaction Number</th>
                        <th class="py-2 px-4 text-left text-white">Reference</th>
                        <th class="py-2 px-4 text-left text-white">Order Number</th>
                        <th class="py-2 px-4 text-left text-white">Accountant</th>
                        <th class="py-2 px-4 text-left text-white">Sales</th>
                        <th class="py-2 px-4 text-left text-white">Total Cash</th>
                        <th class="py-2 px-4 text-left text-white">Total Commission</th>
                        <th class="py-2 px-4 text-left text-white">Total Transfers</th>
                        <th class="py-2 px-4 text-left text-white">Total Expenses</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTransfersSum = 0;
                        $totalExpensesSum = 0;
                    @endphp
                    @foreach($transactions as $transaction)
                        @php
                            $totalTransfers = 0;
                            $totalExpenses = 0;
                            foreach ($transaction->transfers as $transfer) {
                                $totalTransfers += $transfer->transfer_value;
                            }
                            foreach ($transaction->expenses as $expense) {
                                $totalExpenses += $expense->expenses_value;
                            }

                            // Add to global sums
                            $totalTransfersSum += $totalTransfers;
                            $totalExpensesSum += $totalExpenses;
                        @endphp
                        <tr>
                            <td class="py-2 px-4 text-center">{{ $transaction->openclose->id }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->openclose->open_at }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->openclose->close_at ?? 'open' }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->id }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->reference_collection }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->order_number }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->openclose->user->name }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->user->name }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->total_cash }}</td>
                            <td class="py-2 px-4 text-center">{{ $transaction->sales_commission }}</td>
                            <td class="py-2 px-4 text-center">{{ $totalTransfers }}</td>
                            <td class="py-2 px-4 text-center">{{ $totalExpenses }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-200">
                        <td colspan="8" class="py-2 px-4 text-center">Total</td>
                        <td class="py-2 px-4 text-center">{{ $transactions->sum('total_cash') }}</td>
                        <td class="py-2 px-4 text-center">{{ $transactions->sum('sales_commission') }}</td>
                        <td class="py-2 px-4 text-center">{{ $totalTransfersSum }}</td>
                        <td class="py-2 px-4 text-center">{{ $totalExpensesSum }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
