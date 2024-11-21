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
                <!-- Print Button -->
                <div class="text-right mt-4 no-print" style="float: right;">
                    <button onclick="printDiv('printableArea')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Print Report</button>
                </div>
                <p>Open Date: {{ $openClose->open_at }}</p>
                <p>Close Date: {{ $openClose->close_at ?? 'Open' }}</p>
                
                <br><br>

                <!-- Daily Transactions -->
                <div class="section-wrapper transactions-section">
                    <div class="section-title">Daily Transactions</div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Reference Collection</th>
                                <th>Sales</th>
                                <th>Order Number</th>
                                <th>Orders Delivered</th>
                                <th>Total Collection</th>
                                <th>Sales Commission</th>
                                <th>Total After Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalCashSum = 0; @endphp
                            @foreach($openClose->transactions as $transaction)
                                @php
                                    $totalCashSum += $transaction->total_remaining;
                                    $totalCollection = $transaction->total_remaining + $transaction->sales_commission;
                                @endphp
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->reference_collection }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->order_number }}</td>
                                    <td>{{ $transaction->order_delivered }}</td>
                                    <td>{{ $totalCollection }} LE</td>
                                    <td>{{ $transaction->sales_commission }} LE</td>
                                    <td>{{ $transaction->total_remaining }} LE</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-right font-bold">Total Cash:</td>
                                <td class="font-bold">{{ $totalCashSum }} LE</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Sales Transfers -->
                <div class="section-wrapper transfers-section">
                    <div class="section-title">Sales Transfers</div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Transfer Key</th>
                                <th>Transfer Image</th>
                                <th>Transfer Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalTransferValueSum = 0; @endphp
                            @foreach($openClose->transactions as $transaction)
                                @foreach($transaction->transfers as $transfer)
                                    @php $totalTransferValueSum += $transfer->transfer_value; @endphp
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transfer->transfer_key }}</td>
                                        <td>
                                            @if($transfer->image)
                                                <a href="{{ asset($transfer->image) }}" target="_blank">
                                                    <img src="{{ asset($transfer->image) }}" alt="Transfer Image" class="w-20 h-20 object-cover" />
                                                </a>
                                            @else
                                                No image attached
                                            @endif
                                        </td>
                                        <td>{{ $transfer->transfer_value }} LE</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-bold">Total Transfer Value:</td>
                                <td class="font-bold">{{ $totalTransferValueSum }} LE</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Sales Expenses -->
                <div class="section-wrapper sales-expenses-section">
                    <div class="section-title">Sales Expenses</div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Expense Key</th>
                                <th>Expense Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalExpenseValueSum = 0; @endphp
                            @foreach($generalExpenses as $expense)
                                @php $totalExpenseValueSum += $expense->expenses_value; @endphp
                                <tr>
                                    <td>{{ $expense->transaction_id }}</td>
                                    <td>{{ $expense->expenses_key }}</td>
                                    <td>{{ $expense->expenses_value }} LE</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right font-bold">Total Sales Expense Value:</td>
                                <td class="font-bold">{{ $totalExpenseValueSum }} LE</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Accountant Expenses -->
                <div class="section-wrapper accountant-expenses-section">
                    <div class="section-title">Accountant Expenses</div>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Expense Key</th>
                                <th>Expense Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalMyExpenseValueSum = 0; @endphp
                            @foreach($accountingExpenses as $expense)
                                @php $totalMyExpenseValueSum += $expense->expenses_value; @endphp
                                <tr>
                                    <td>{{ $expense->transaction_id }}</td>
                                    <td>{{ $expense->expenses_key }}</td>
                                    <td>{{ $expense->expenses_value }} LE</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right font-bold">Total Accountant Expenses:</td>
                                <td class="font-bold">{{ $totalMyExpenseValueSum }} LE</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Final Calculation -->
                <div class="mt-8 text-right">
                    <h4 class="font-semibold text-xl inline-flex items-center">
                        Final Cash Calculation:
                    </h4>
                    <p class="font-bold">{{ $totalCashSum - ($totalTransferValueSum + $totalExpenseValueSum + $totalMyExpenseValueSum) }} LE</p>
                </div>

                
            </div>
        </div>
    </div>

    <style>
        .section-wrapper {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .transactions-section {
            background-color: #f9f9f9;
            border-left: 4px solid #007BFF;
        }

        .transfers-section {
            background-color: #fff3cd;
            border-left: 4px solid #FFC107;
        }

        .sales-expenses-section {
            background-color: #f2f9f2;
            border-left: 4px solid #28a745;
        }

        .accountant-expenses-section {
            background-color: #f8f9fa;
            border-left: 4px solid #6c757d;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
        }

        .styled-table th, .styled-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .styled-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
    </style>

    <script>
        function printDiv(divId) {
            const printableContent = document.getElementById(divId).cloneNode(true); // Clone the content
            const printWindow = window.open('', '_blank'); // Open a new window for printing
            printWindow.document.open(); // Open the document for writing

            // Inject the cloned content into a clean HTML structure
            printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <title>Report</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            padding: 0;
                        }
                        h3, h4 {
                            font-size: 18px;
                            font-weight: bold;
                            margin: 20px 0 10px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        th, td {
                            text-align: center;
                            padding: 10px;
                            border: 1px solid #ddd;
                        }
                        th {
                            background-color: #f1f1f1;
                            font-weight: bold;
                        }
                        .section-wrapper {
                            margin-bottom: 20px;
                            padding: 15px;
                            border-radius: 8px;
                            border: 1px solid #ccc;
                        }
                        .transactions-section {
                            background-color: #f9f9f9;
                            border-left: 4px solid #007BFF;
                        }
                        .transfers-section {
                            background-color: #fff3cd;
                            border-left: 4px solid #FFC107;
                        }
                        .expenses-section {
                            background-color: #f2f9f2;
                            border-left: 4px solid #28a745;
                        }
                        .no-print {
                            display: none !important; /* Hide elements with the no-print class */
                        }
                    </style>
                </head>
                <body>
                    ${printableContent.innerHTML} <!-- Insert the cloned content -->
                </body>
                </html>
            `);

            printWindow.document.close(); // Close the document for writing
            printWindow.focus(); // Focus the new window
            printWindow.print(); // Trigger the print dialog
            printWindow.close(); // Close the print window after printing
        }

    </script>
</x-app-layout>
