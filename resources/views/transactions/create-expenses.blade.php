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

                    <div id="expense_fields">
                        <div class="flex items-center mb-4">
                            <input type="text" class="form-input border rounded px-3" name="expense_keys[]" placeholder="Expense Name" style="width:45%;">
                            <span class="mx-2">-</span>
                            <input type="number" class="form-input border rounded px-3" name="expense_values[]" placeholder="Expense Value" style="width:45%;">
                        </div>
                    </div>
                    <button type="button" class="add-expense-btn bg-gray-500 text-white px-4 py-2 rounded">+ Add Expense</button>
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
                        <input type="text" class="form-input border rounded px-3" name="expense_keys[]" placeholder="Expense Name" style="width:45%;">
                        <span class="mx-2">-</span>
                        <input type="number" class="form-input border rounded px-3" name="expense_values[]" placeholder="Expense Value" style="width:45%;">
                    </div>
                `);
            });
        });
    </script>
</x-app-layout>
