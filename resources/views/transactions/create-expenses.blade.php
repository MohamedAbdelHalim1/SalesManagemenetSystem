<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add My Expenses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <input type="hidden" name="reference_collection" value="">
                    <input type="hidden" name="order_number" value="">
                    <input type="hidden" name="order_delivered" value="">
                    <input type="hidden" name="total_cash" value="">
                    <input type="hidden" name="commission_value" value="">
                    <input type="hidden" name="remaining" value="">
                    <input type="hidden" name="cash_equivalent" id="cash_equivalent" value="0">


                    <div id="expense_fields">
                        <div class="flex items-center mb-4">
                            <input type="text" class="form-input border rounded px-3" name="expense_keys[]" placeholder="Expense Name" style="width:45%;" required>
                            <span class="mx-2">-</span>
                            <input type="number" class="form-input border rounded px-3" name="expense_values[]" placeholder="Expense Value" style="width:45%;" required>
                        </div>
                    </div>
                    <button type="button" class="add-expense-btn bg-gray-500 text-white px-4 py-2 rounded">+ Add Expense</button>

                     <!-- Coin Input Table -->
                     <div class="mb-6">
                        <table class="w-full border rounded text-center">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4">200</th>
                                    <th class="py-2 px-4">100</th>
                                    <th class="py-2 px-4">50</th>
                                    <th class="py-2 px-4">20</th>
                                    <th class="py-2 px-4">10</th>
                                    <th class="py-2 px-4">5</th>
                                    <th class="py-2 px-4">1</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" id="coin_200" name="coin_200" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                    <td><input type="number" id="coin_100" name="coin_100" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                    <td><input type="number" id="coin_50" name="coin_50" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                    <td><input type="number" id="coin_20" name="coin_20" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                    <td><input type="number" id="coin_10" name="coin_10" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                    <td><input type="number" id="coin_5" name="coin_5" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                    <td><input type="number" id="coin_1" name="coin_1" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="bg-gray-500 text-white px-4 py-2 mt-4 rounded">Submit Expenses</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.add-expense-btn').on('click', function () {
                $('#expense_fields').append(`
                    <div class="flex items-center mb-4">
                        <input type="text" class="form-input border rounded px-3" name="expense_keys[]" placeholder="Expense Name" style="width:45%;" required>
                        <span class="mx-2">-</span>
                        <input type="number" class="form-input border rounded px-3" name="expense_values[]" placeholder="Expense Value" style="width:45%;" required>
                    </div>
                `);
            });
        });
    </script>
</x-app-layout>
