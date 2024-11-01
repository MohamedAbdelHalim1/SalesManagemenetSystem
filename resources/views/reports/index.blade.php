<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <button onclick="printReport()" class="bg-gray-500 text-white px-4 py-2 rounded mb-4" style="float: right;">
                    Print Report
                </button>
                @foreach($openCloses as $openClose)
                    <div class="mb-6">
                        <h4 class="text-xl font-semibold">{{ $openClose->user->name }}'s Report Number ({{ $loop->iteration }})</h4>
                        <p>Opened At: {{ $openClose->open_at }}</p>
                        <p>Closed At: {{ $openClose->close_at }}</p>

                        <h5 class="mt-4 font-semibold">Transactions:</h5>
                        @if($openClose->transactions->isEmpty())
                            <p>No transactions available for this session.</p>
                        @else
                            <!-- DataTable for Transactions -->
                            <table class="w-full border rounded mt-2 datatable">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4">Reference Collection</th>
                                        <th class="py-2 px-4">Sales Name</th>
                                        <th class="py-2 px-4">Order Number</th>
                                        <th class="py-2 px-4">Orders Delivered</th>
                                        <th class="py-2 px-4">Total Cash</th>
                                        <th class="py-2 px-4">Sales Commission</th>
                                        <th class="py-2 px-4">Total Remaining</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($openClose->transactions as $transaction)
                                        <tr class="border-t">
                                            <td class="py-2 px-4 text-center">{{ $transaction->reference_collection }}</td>
                                            <td class="py-2 px-4 text-center">{{ $transaction->user->name }}</td>
                                            <td class="py-2 px-4 text-center">{{ $transaction->order_number }}</td>
                                            <td class="py-2 px-4 text-center">{{ $transaction->order_delivered }}</td>
                                            <td class="py-2 px-4 text-center">{{ $transaction->total_cash }}</td>
                                            <td class="py-2 px-4 text-center">{{ $transaction->sales_commission }}</td>
                                            <td class="py-2 px-4 text-center">{{ $transaction->total_remaining }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Coins Table -->
                            <h5 class="font-semibold mt-6 mb-4">Coins</h5>
                            <table class="min-w-full bg-white coinsTable">
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
                                            <td colspan="8" class="py-2 px-4 border-b text-center">No coin data available.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                        @endif
                    </div>
                    <hr style="margin: 15px;">
                @endforeach
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
            $('.coinsTable').DataTable(); // Initialize all tables with the class coinsTable
        });

        function printReport() {
            window.print();
        }

    </script>
</x-app-layout>
