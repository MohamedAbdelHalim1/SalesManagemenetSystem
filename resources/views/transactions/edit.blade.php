<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; flex-direction:row; justify-content:space-between; padding:40px;">
                        <div style="display: flex; flex-direction:column; justify-content:space-around;">
                            <div class="mb-4">
                                <label for="reference_collection" class="block font-semibold mb-2">Reference Collection</label>
                                <input type="number" class="form-input border rounded px-3" id="reference_collection" name="reference_collection" value="{{ $transaction->reference_collection }}">
                            </div>
    
                            <div class="mb-4">
                                <label for="order_number" class="block font-semibold mb-2">Number Of Orders</label>
                                <input type="number" class="form-input border rounded px-3" id="order_number" name="order_number" value="{{ $transaction->order_number }}">
                            </div>
    
                            <div class="mb-4">
                                <label for="order_delivered" class="block font-semibold mb-2">Orders Delivered</label>
                                <input type="number" class="form-input border rounded px-3" id="order_delivered" name="order_delivered" value="{{ $transaction->order_delivered }}">
                            </div>
                        </div>
                        <div style="display: flex; flex-direction:column; justify-content:space-around;">
                            <div class="mb-4">
                                <label for="total_cash" class="block font-semibold mb-2">Total Collection</label>
                                <input type="number" class="form-input border rounded px-3" id="total_cash" name="total_cash" value="{{ $transaction->total_cash }}">
                            </div>
        
                            <div class="mb-4">
                                <label for="sales_commission" class="block font-semibold mb-2">Commission Value</label>
                                <input type="number" class="form-input border rounded px-3" id="sales_commission" name="sales_commission" value="{{ $transaction->sales_commission }}">
                            </div>
        
                            <div class="mb-4">
                                <label for="total_remaining" class="block font-semibold mb-2">Total Remaining</label>
                                <input type="number" class="form-input border rounded px-3" id="total_remaining" name="total_remaining" value="{{ $transaction->total_remaining }}" readonly>
                            </div>    
                        </div>
                    </div>

                    <!-- Transfers Section -->
                    <div class="mb-4" style="border-bottom:1px solid #ddd; padding:15px;">
                        <label for="transfers" class="block font-semibold mb-2">Transfers</label>
                        <div id="transfer_fields">
                            <!-- Existing Transfers -->
                            @foreach($transaction->transfers as $transfer)
                                <div class="flex items-center space-x-2 mb-4">
                                    <!-- Hidden Transfer ID -->
                                    <input type="hidden" name="transfer_ids[]" value="{{ $transfer->id }}">

                                    <!-- Transfer Key Input -->
                                    <input type="text" class="form-input border rounded px-3" placeholder="Transfer Method" style="width:20%;" name="transfer_keys[{{ $transfer->id }}]" value="{{ $transfer->transfer_key }}">
                                    
                                    <!-- Divider -->
                                    <span>-</span>

                                    <!-- Transfer Value Input -->
                                    <input type="number" class="form-input border rounded px-3 transfer-value" placeholder="Transfer Value" style="width:15%;" name="transfer_values[{{ $transfer->id }}]" value="{{ $transfer->transfer_value }}">

                                    <!-- Image Preview and Upload -->
                                    <div class="flex items-center space-x-2">
                                        @if($transfer->image)
                                            <a href="{{ asset($transfer->image) }}" target="_blank">
                                                <img src="{{ asset($transfer->image) }}" alt="Transfer Image" class="w-16 h-16 object-cover">
                                            </a>
                                        @else
                                            <span>No image attached</span>
                                        @endif
                                        <input type="file" class="form-input border rounded px-3 ml-2" name="transfer_images[{{ $transfer->id }}]">
                                    </div>

                                    <!-- Remove Button -->
                                    <button type="button" class="bg-gray-500 remove-transfer-btn text-red-500 font-semibold px-2 rounded">-</button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Button to Add New Transfer -->
                        <button type="button" class="add-transfer-btn bg-gray-500 text-white font-semibold px-2 rounded hover:bg-blue-700">+ Add Transfer</button>
                    </div>



                    <!-- Expenses Section -->
                    <div class="mb-4" style="border-bottom:1px solid #ddd; padding:15px;">
                        <label for="expenses" class="block font-semibold mb-2">Expenses</label>
                        <div id="expense_fields">
                            @foreach($transaction->expenses as $expense)
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" class="form-input border rounded px-3" placeholder="Expense Name" style="width:45%;" name="expense_keys[]" value="{{ $expense->expenses_key }}">
                                    <span>-</span>
                                    <input type="number" class="form-input border rounded px-3 expense-value" placeholder="Expense Value" style="width:45%;" name="expense_values[]" value="{{ $expense->expenses_value }}">
                                    <button type="button" class="bg-gray-500 remove-expense-btn text-red-500 font-semibold px-2 rounded">-</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="add-expense-btn bg-gray-500 text-white font-semibold px-2 rounded hover:bg-blue-700">+ Add Expense</button>
                    </div>

                    <!-- Cash Equivalent Display -->
                    <div class="flex justify-between items-center mb-6">
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded">Save Changes</button>
                        <div class="text-right">
                            <h2 class="text-lg font-semibold">Your cash should equal</h2>
                            <input type="number" id="cash_equivalent" class="form-input border rounded px-3 mt-2 text-center" style="width:150px;" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Live Calculation Script -->
    <script>
        $(document).ready(function() {
            function calculateRemainingAndCashEquivalent() {
                const totalCash = parseFloat($('#total_cash').val()) || 0;
                const commission = parseFloat($('#sales_commission').val()) || 0;
                const remaining = totalCash - commission;
                $('#total_remaining').val(remaining.toFixed(2));

                let transferTotal = 0;
                let expenseTotal = 0;
                
                $('.transfer-value').each(function() {
                    transferTotal += parseFloat($(this).val()) || 0;
                });

                $('.expense-value').each(function() {
                    expenseTotal += parseFloat($(this).val()) || 0;
                });

                const cashEquivalent = remaining - transferTotal - expenseTotal;
                $('#cash_equivalent').val(cashEquivalent.toFixed(2));
            }

            // Update calculations when values change
            $('#total_cash, #sales_commission').on('input', calculateRemainingAndCashEquivalent);
            $(document).on('input', '.transfer-value, .expense-value', calculateRemainingAndCashEquivalent);

            // Add Transfer Row
            $('.add-transfer-btn').on('click', function() {
                const transferHtml = `
                    <div class="flex items-center space-x-2 mb-2 new-transfer">
                        <input type="text" class="form-input border rounded px-3" placeholder="Transfer Method" style="width:20%;" name="new_transfer_keys[]">
                        <span>-</span>
                        <input type="number" class="form-input border rounded px-3 transfer-value" placeholder="Transfer Value" style="width:15%;" name="new_transfer_values[]">
                        <input type="file" class="form-input border rounded px-3" style="width:35%;" name="new_transfer_images[]">
                        <button type="button" class="bg-gray-500 remove-transfer-btn text-red-500 font-semibold px-2 rounded">-</button>
                    </div>`;
                $('#transfer_fields').append(transferHtml);
            });


            // Remove Transfer Row
            $('#transfer_fields').on('click', '.remove-transfer-btn', function() {
                $(this).closest('div').remove();
                calculateRemainingAndCashEquivalent();
            });

            // Add Expense Row
            $('.add-expense-btn').on('click', function() {
                const expenseHtml = `
                    <div class="flex items-center space-x-2 mb-2">
                        <input type="text" class="form-input border rounded px-3" placeholder="Expense Name" style="width:45%;" name="expense_keys[]">
                        <span>-</span>
                        <input type="number" class="form-input border rounded px-3 expense-value" placeholder="Expense Value" style="width:45%;" name="expense_values[]">
                        <button type="button" class="bg-gray-500 remove-expense-btn text-red-500 font-semibold px-2 rounded">-</button>
                    </div>`;
                $('#expense_fields').append(expenseHtml);
                calculateRemainingAndCashEquivalent();
            });

            // Remove Expense Row
            $('#expense_fields').on('click', '.remove-expense-btn', function() {
                $(this).closest('div').remove();
                calculateRemainingAndCashEquivalent();
            });

            // Initial calculation
            calculateRemainingAndCashEquivalent();
        });
    </script>
</x-app-layout>
