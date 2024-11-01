<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Upper Part: Accounting Report Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-4">Accounting Report Summary</h3>
                    <!-- Accounting Report Table -->
                    <table id="accountingReportTable" class="w-full border rounded datatable">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4">Accountant Name</th>
                                <th class="py-2 px-4">Number of Open/Close Days</th>
                                <th class="py-2 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accountingUsers as $user)
                                <tr>
                                    <td class="py-2 px-4 text-center">{{ $user->name }}</td>
                                    <td class="py-2 px-4 text-center">{{ $user->open_closes_count }}</td>
                                    <td class="py-2 px-4 text-center">
                                        <a href="{{ route('reports.index', $user->id) }}" class="bg-gray-500 text-white px-3 py-1 rounded">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Lower Part: Sales Report Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-4">Sales Report Summary</h3>
                    <!-- Sales Report Table -->
                    <table id="salesReportTable" class="w-full border rounded datatable">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4">Sales Name</th>
                                <th class="py-2 px-4">Number of Transactions</th>
                                <th class="py-2 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesUsers as $user)
                                <tr>
                                    <td class="py-2 px-4 text-center">{{ $user->name }}</td>
                                    <td class="py-2 px-4 text-center">{{ $user->transactions_count }}</td>
                                    <td class="py-2 px-4 text-center">
                                        <a href="{{ route('reports.sales', $user->id) }}" class="bg-gray-500 text-white px-3 py-1 rounded">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            $('#accountingReportTable').DataTable();
            $('#salesReportTable').DataTable();
        });
    </script>
</x-app-layout>
