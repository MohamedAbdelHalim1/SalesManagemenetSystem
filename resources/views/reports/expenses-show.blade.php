<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expenses Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto bg-white shadow-lg rounded-lg p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Expense Information</h3>

            <table class="min-w-full table-auto bg-gray-100 rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Expense Key</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Expense Value</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Transaction ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 text-sm text-gray-700">{{ $expense->expenses_key }}</td>
                            <td class="py-2 px-4 text-sm text-gray-700">{{ number_format($expense->expenses_value, 2) }} LE</td>
                            <td class="py-2 px-4 text-sm text-gray-700">{{ $expense->transaction->id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
