<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto">
            <div class="bg-white shadow rounded-lg p-6" id="printableArea">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6 no-print">
                    <h3 class="font-semibold text-lg">Transaction Information</h3>
                    <div class="flex space-x-4">
                        <button onclick="printDiv('printableArea')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Print Report
                        </button>
                    </div>
                </div>

                <!-- OpenClose Information -->
                <p>Open Date: {{ $openClose->open_at }}</p>
                <p>Close Date: {{ $openClose->close_at ?? 'Open' }}</p>

                <!-- Sections -->
                <div class="mt-6 space-y-6">
                    <!-- Daily Transactions -->
                    <div class="section-wrapper">
                        <h4 class="section-title">Daily Transactions</h4>
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
                                @php
                                    $totalCashSum = 0;
                                    $totalCollectionSum=0; 
                                    $totalCommissionSum = 0; 
                                @endphp
                                @foreach($openClose->transactions as $transaction)
                                    @php
                                        $totalCashSum += $transaction->total_remaining;
                                        $totalCollectionSum += $transaction->total_remaining + $transaction->sales_commission;
                                        $totalCommissionSum += $transaction->sales_commission;
                                        
                                        $totalCollection = $transaction->total_remaining + $transaction->sales_commission;
                                    @endphp
                                    @if ($transaction->user->role_id == 2)
                                    @continue
                                    @endif
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->reference_collection }}</td>
                                            <td>{{ $transaction->user->name }}</td>
                                            <td>{{ $transaction->order_number }}</td>
                                            <td>{{ $transaction->order_delivered }}</td>
                                            <td>{{ $totalCollection ?? 0 }} LE</td>
                                            <td>{{ $transaction->sales_commission ?? 0}} LE</td>
                                            <td>{{ $transaction->total_remaining ?? 0}} LE</td>
                                        </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right font-bold">Total Cash:</td>
                                    <td class="font-bold">{{ $totalCollectionSum }} LE</td>
                                    <td class="font-bold">{{ $totalCommissionSum }} LE</td>
                                    <td class="font-bold">{{ $totalCashSum }} LE</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Transfers Section -->
                    <div class="section-wrapper transfers-section">
                        <div class="section-title">Sales Transfers</div>
                        <table id="transfersTable" class="styled-table">
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
                                        @if ($transfer->transfer_key == Null && $transfer->transfer_value == Null) 
                                            @continue
                                        @endif
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

                    <!-- General Expenses -->
                    <div class="section-wrapper">
                        <h4 class="section-title">General Expenses</h4>
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Expense Key</th>
                                    <th>Expense Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $totalGeneralExpenseSum = 0; 
                                @endphp
                                @foreach($generalExpenses as $expense)
                                    @php $totalGeneralExpenseSum += $expense->expenses_value; @endphp
                                    @if ($expense->expenses_key == Null && $expense->expenses_value == Null)
                                        @continue
                                    @endif
                                    <tr>
                                        <td>{{ $expense->transaction_id }}</td>
                                        <td>{{ $expense->expenses_key }}</td>
                                        <td>{{ $expense->expenses_value }} LE</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right font-bold">Total General Expenses:</td>
                                    <td class="font-bold">{{ $totalGeneralExpenseSum }} LE</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Accountant Expenses -->
                    <div class="section-wrapper">
                        <h4 class="section-title">Accountant Expenses</h4>
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Expense Key</th>
                                    <th>Expense Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalAccountantExpenseSum = 0; @endphp
                                @foreach($accountingExpenses as $expense)
                                    @php $totalAccountantExpenseSum += $expense->expenses_value; @endphp
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
                                    <td class="font-bold">{{ $totalAccountantExpenseSum }} LE</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Coins Distribution -->
                    <div class="section-wrapper">
                        <h4 class="section-title">Coins Distribution</h4>
                        <table class="styled-table">
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
                                            <td>{{ $coin->coin_200 ?? 0 }}</td>
                                            <td>{{ $coin->coin_100 ?? 0 }}</td>
                                            <td>{{ $coin->coin_50 ?? 0 }}</td>
                                            <td>{{ $coin->coin_20 ?? 0 }}</td>
                                            <td>{{ $coin->coin_10 ?? 0 }}</td>
                                            <td>{{ $coin->coin_5 ?? 0 }}</td>
                                            <td>{{ $coin->coin_1 ?? 0 }}</td>
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
                </div>
            </div>
        </div>
    </div>

    <style>
        .section-wrapper {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
        }

        .styled-table th,
        .styled-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .styled-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        /* Hide elements during printing */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                font-size: 14px;
            }

            .section-wrapper {
                page-break-inside: avoid;
            }
        }
    </style>

    <script>
        function printDiv(divId) {
            const printableContent = document.getElementById(divId).cloneNode(true);
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Report</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 20px;
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
                                page-break-inside: avoid;
                            }
                        </style>
                    </head>
                    <body>${printableContent.innerHTML}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</x-app-layout>
