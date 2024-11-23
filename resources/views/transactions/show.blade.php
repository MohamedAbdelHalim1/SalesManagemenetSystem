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
                <div class="text-right mt-4 no-print" style="float:right;display:flex;">
                    <button onclick="printDiv('printableArea')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-700" style="margin-right: 10px;">Print Report</button>
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
                <br><br>

                <!-- Transactions Section -->
                <div class="section-wrapper transactions-section">
                    <div class="section-title">Daily Transactions</div>
                    <table id="transactionsTable">
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
                                    <td>{{ $totalCollection ?? 0}} LE</td>
                                    <td>{{ $transaction->sales_commission ?? 0}} LE</td>
                                    <td>{{ $transaction->total_remaining ?? 0}} LE</td>
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

                <!-- Transfers Section -->
                <div class="section-wrapper transfers-section">
                    <div class="section-title">Sales Transfers</div>
                    <table id="transfersTable">
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

                <!-- Sales Expenses Section -->
                <div class="section-wrapper sales-expenses-section">
                    <div class="section-title">Sales Expenses</div>
                    <table id="expensesTable">
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

                <!-- Accountant Expenses Section -->
                <div class="section-wrapper accountant-expenses-section">
                    <div class="section-title">Accountant Expenses</div>
                    <table id="myExpensesTable">
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


              <!-- Coins Section -->
                <div class="section-wrapper coins-section">
                    <div class="section-title">Coins Distribution</div>
                    <table id="coinsTable">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Coin 200</th>
                                <th>Coin 100</th>
                                <th>Coin 50</th>
                                <th>Coin 20</th>
                                <th>Coin 10</th>
                                <th>Coin 5</th>
                                <th>Coin 1</th>
                                <th>Total Coins Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalCoinsSum = 0; @endphp
                            @foreach($openClose->transactions as $transaction)
                                @if ($transaction->coin)
                                    @php
                                        $coin = $transaction->coin;
                                        $totalCoinsValue =
                                            ($coin->coin_200 * 200) +
                                            ($coin->coin_100 * 100) +
                                            ($coin->coin_50 * 50) +
                                            ($coin->coin_20 * 20) +
                                            ($coin->coin_10 * 10) +
                                            ($coin->coin_5 * 5) +
                                            ($coin->coin_1 * 1);
                                        $totalCoinsSum += $totalCoinsValue;
                                    @endphp
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $coin->coin_200 ?? 0}}</td>
                                        <td>{{ $coin->coin_100 ?? 0}}</td>
                                        <td>{{ $coin->coin_50 ?? 0}}</td>
                                        <td>{{ $coin->coin_20 ?? 0}}</td>
                                        <td>{{ $coin->coin_10 ?? 0}}</td>
                                        <td>{{ $coin->coin_5 ?? 0}}</td>
                                        <td>{{ $coin->coin_1 ?? 0}}</td>
                                        <td>{{ $totalCoinsValue }} LE</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td colspan="8">No coin data available for this transaction.</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="text-right font-bold">Total Coins Value:</td>
                                <td class="font-bold">{{ $totalCoinsSum }} LE</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>



                <!-- Final Calculation -->
                <div class="mt-8 text-right" style="float: left;">
                    <h4 class="font-semibold text-xl inline-flex items-center">
                        Final Cash Calculation:
                        <span class="tooltip-container ml-2">
                            <i class="tooltip-icon">?</i>
                            <span class="tooltip-text">
                                This number is calculated as:
                                <br>
                                Total Cash After Commission - (Total Transfers + Total Expenses (Sales + Accountants))
                            </span>
                        </span>
                    </h4>
                    <p class="font-bold">{{ $totalCashSum - ($totalTransferValueSum + $totalExpenseValueSum + $totalMyExpenseValueSum) }} LE</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* General Section Styling */
        .section-wrapper {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        
        /* Specific Section Colors */
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
        
        /* Table Styling */
        .section-wrapper table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .section-wrapper th, .section-wrapper td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .section-wrapper th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        
        /* Header Titles for Sections */
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .coins-section {
            background-color: #eef7fa;
            border-left: 4px solid #17a2b8;
        }


          /* Tooltip styling */
    .tooltip-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .tooltip-icon {
        display: inline-block;
        width: 15px;
        height: 15px;
        background-color: #007BFF;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        line-height: 18px;
        border-radius: 50%;
        margin-left: 5px;
        font-style: normal;
    }

    .tooltip-text {
        display: none;
        position: absolute;
        bottom: 125%; /* Position above the tooltip icon */
        left: 50%;
        transform: translateX(-50%);
        width: 200px;
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        font-size: 12px;
        line-height: 1.4;
        z-index: 100;
    }

    .tooltip-text::after {
        content: '';
        position: absolute;
        top: 100%; /* Arrow pointing downwards */
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }

    .tooltip-container:hover .tooltip-text {
        display: block;
    }
    </style>


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
            $('#coinsTable').DataTable({
                searching: false,
                paging: false,
                info: false
            });
        });


        function printDiv(divId) {
            const printableContent = document.getElementById(divId).cloneNode(true); // Clone the content
            const printWindow = window.open('', '_blank'); // Open a new window for printing
            printWindow.document.open(); // Open the document for writing

            // Write the HTML structure for the print window
            printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <title>Print Preview</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            padding: 0;
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
                        .section-title {
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 10px;
                            color: #333;
                        }
                        .tooltip-container {
                            display: none;
                        }
                        .no-print {
                            display: none !important;
                        }
                    </style>
                </head>
                <body>
                    ${printableContent.innerHTML} <!-- Insert the cloned content -->
                </body>
                </html>
            `);

            printWindow.document.close(); // Close the document
            printWindow.focus(); // Focus on the new window
            printWindow.print(); // Trigger the print dialog
            printWindow.close(); // Close the print window after printing
        }


    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Calculate the Final Cash
            const totalCashSum = {{ $totalCashSum }};
            const totalTransferValueSum = {{ $totalTransferValueSum }};
            const totalExpenseValueSum = {{ $totalExpenseValueSum }};
            const totalMyExpenseValueSum = {{ $totalMyExpenseValueSum }};
            const finalCash = totalCashSum - (totalTransferValueSum + totalExpenseValueSum + totalMyExpenseValueSum);
    
            // Set the value of the hidden input field
            const totalCashInput = document.getElementById('total_cash');
            if (totalCashInput) {
                totalCashInput.value = finalCash;
            }
        });
    </script>
    
</x-app-layout>
