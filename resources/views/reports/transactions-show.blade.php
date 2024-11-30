<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto bg-white shadow-lg rounded-lg p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Transaction Information</h3>

            <table class="min-w-full table-auto bg-gray-100 rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Reference Collection</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Order Number</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Order Delivered</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Total Cash</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Sales Commission</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Total Remaining</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">User</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Open/Close ID</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $transaction->reference_collection }}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $transaction->order_number }}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $transaction->order_delivered}}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ number_format($transaction->total_cash, 2) }} LE</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ number_format($transaction->sales_commission, 2) }} LE</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ number_format($transaction->total_remaining, 2) }} LE</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $transaction->user->name }}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $transaction->openclose->id }}</td>
                    </tr>
                </tbody>
               
            </table>
        </div>
    </div>
</x-app-layout>
