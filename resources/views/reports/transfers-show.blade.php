<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transfer Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto bg-white shadow-lg rounded-lg p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Transfer Information</h3>

            <table class="min-w-full table-auto bg-gray-100 rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Transfer Key</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Transfer Value</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Image</th>
                        <th class="py-2 px-4 text-left text-sm text-gray-700">Transaction ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 text-sm text-gray-700">{{ $transfer->transfer_key }}</td>
                            <td class="py-2 px-4 text-sm text-gray-700">{{ number_format($transfer->transfer_value, 2) }} LE</td>
                            <td class="py-2 px-4 text-sm text-gray-700">
                                @if($transfer->image)
                                    <img src="{{ asset('storage/' . $transfer->image) }}" alt="Transfer Image" class="w-24 h-24 object-cover rounded-md">
                                @else
                                    <span>No image</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 text-sm text-gray-700">{{ $transfer->transaction->id }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
