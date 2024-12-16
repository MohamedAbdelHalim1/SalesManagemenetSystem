<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="transactionUpdateForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Transaction Fields -->
                    <div style="display: flex; flex-direction:row; justify-content:space-between; padding:40px;">
                        <div style="display: flex; flex-direction:column; justify-content:space-around;">
                            <div class="mb-4">
                                <label for="reference_collection" class="block font-semibold mb-2">Reference Collection - عمليه التحصيل</label>
                                <input type="number" class="form-input border rounded px-3" id="reference_collection" name="reference_collection" value="{{ $transaction->reference_collection }}">
                            </div>
                            <div class="mb-4">
                                <label for="order_number" class="block font-semibold mb-2">Number Of Orders - عدد الاوردرات</label>
                                <input type="number" class="form-input border rounded px-3" id="order_number" name="order_number" value="{{ $transaction->order_number }}">
                            </div>
                            <div class="mb-4">
                                <label for="order_delivered" class="block font-semibold mb-2">Orders Delivered - تم التسليم</label>
                                <input type="number" class="form-input border rounded px-3" id="order_delivered" name="order_delivered" value="{{ $transaction->order_delivered }}">
                            </div>
                        </div>
                        <div style="display: flex; flex-direction:column; justify-content:space-around;">
                            <div class="mb-4">
                                <label for="total_cash" class="block font-semibold mb-2">Total Collection - اجمالي التحصيل</label>
                                <input type="number" class="form-input border rounded px-3" id="total_cash" name="total_cash" value="{{ $transaction->total_remaining + $transaction->sales_commission }}">
                            </div>
                            <div class="mb-4">
                                <label for="sales_commission" class="block font-semibold mb-2">Commission Value - عمولة المندوب</label>
                                <input type="number" class="form-input border rounded px-3" id="sales_commission" name="sales_commission" value="{{ $transaction->sales_commission }}">
                            </div>
                            <div class="mb-4">
                                <label for="total_remaining" class="block font-semibold mb-2">Total Remaining - المتبقي بعد خصم العموله</label>
                                <input type="number" class="form-input border rounded px-3" id="total_remaining" name="total_remaining" value="{{ $transaction->total_remaining }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Transfers Section -->
                    <div class="mb-4" style="border-bottom:1px solid #ddd; padding:15px;">
                        <label for="transfers" class="block font-semibold mb-2">Transfers - التحويلات</label>
                        <div id="transfer_fields">
                            @foreach($transaction->transfers as $transfer)
                                <div class="flex items-center space-x-2 mb-4 transfer-row">
                                    <input type="hidden" name="transfer_ids[]" value="{{ $transfer->id }}">
                                    <input type="text" class="form-input border rounded px-3" placeholder="Transfer Method" style="width:20%;" name="transfer_keys[{{ $transfer->id }}]" value="{{ $transfer->transfer_key }}" required>
                                    <span>-</span>
                                    <input type="number" class="form-input border rounded px-3 transfer-value" placeholder="Transfer Value" style="width:15%;" name="transfer_values[{{ $transfer->id }}]" value="{{ $transfer->transfer_value }}" required>
                                    @if($transfer->image)
                                        <a href="{{ asset($transfer->image) }}" target="_blank">
                                            <img src="{{ asset($transfer->image) }}" alt="Transfer Image" class="w-16 h-16 object-cover">
                                        </a>
                                    @endif
                                    <input type="file" class="form-input border rounded px-3" name="transfer_images[{{ $transfer->id }}]">
                                    <button type="button" class="bg-gray-500 remove-transfer-btn text-red-500 font-semibold px-2 rounded" data-id="{{ $transfer->id }}">-</button>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="deleted_transfers" name="deleted_transfers" value="">
                        <button type="button" class="add-transfer-btn bg-gray-500 text-white font-semibold px-2 rounded hover:bg-blue-700">+ Add Transfer</button>
                    </div>

                    <!-- Expenses Section -->
                    <div class="mb-4" style="border-bottom:1px solid #ddd; padding:15px;">
                        <label for="expenses" class="block font-semibold mb-2">Expenses - المصاريف</label>
                        <div id="expense_fields">
                            @foreach($transaction->expenses as $expense)
                                <div class="flex items-center space-x-2 mb-2 expense-row">
                                    <input type="hidden" name="expense_ids[]" value="{{ $expense->id }}">
                                    <input type="text" class="form-input border rounded px-3" placeholder="Expense Name" style="width:45%;" name="expense_keys[{{ $expense->id }}]" value="{{ $expense->expenses_key }}" required>
                                    <span>-</span>
                                    <input type="number" class="form-input border rounded px-3 expense-value" placeholder="Expense Value" style="width:45%;" name="expense_values[{{ $expense->id }}]" value="{{ $expense->expenses_value }}" required>
                                    <button type="button" class="bg-gray-500 remove-expense-btn text-red-500 font-semibold px-2 rounded" data-id="{{ $expense->id }}">-</button>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="deleted_expenses" name="deleted_expenses" value="">
                        <button type="button" class="add-expense-btn bg-gray-500 text-white font-semibold px-2 rounded hover:bg-blue-700">+ Add Expense</button>
                    </div>

                      <!-- Coin Distribution Section -->
                      <div class="mb-4" style="border-bottom:1px solid #ddd; padding:15px;">
                        <label for="coins" class="block font-semibold mb-2">Coin Distribution</label>
                        <div class="grid grid-cols-4 gap-4" style="display: flex;">
                            <div>
                                <label for="coin_200" class="block">Coin 200</label>
                                <input type="number" name="coin_200" id="coin_200" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_200 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_100" class="block">Coin 100</label>
                                <input type="number" name="coin_100" id="coin_100" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_100 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_50" class="block">Coin 50</label>
                                <input type="number" name="coin_50" id="coin_50" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_50 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_20" class="block">Coin 20</label>
                                <input type="number" name="coin_20" id="coin_20" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_20 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_10" class="block">Coin 10</label>
                                <input type="number" name="coin_10" id="coin_10" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_10 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_5" class="block">Coin 5</label>
                                <input type="number" name="coin_5" id="coin_5" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_5 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_1" class="block">Coin 1</label>
                                <input type="number" name="coin_1" id="coin_1" class="form-input border rounded px-3" value="{{ $transaction->coin->coin_1 ?? 0 }}" style="width: 80px;">
                            </div>
                            <div>
                                <label for="coin_total" class="block">Coin Total</label>
                                <input type="number" id="coin_total" class="form-input border rounded px-3" readonly style="width: 80px;">
                              </div>
                        </div>
                    </div>


                    <!-- Cash Equivalent Display -->
                    <div class="flex justify-between items-center mb-6">
                        <button type="button" id="updateTransactionBtn" class="bg-gray-500 text-white px-4 py-2 rounded">Save Changes</button>
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
    <script>
        $(document).ready(function() {
            // Calculate Remaining and Cash Equivalent
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

            // Track deleted transfers
            $('#transfer_fields').on('click', '.remove-transfer-btn', function() {
                const transferId = $(this).data('id');
                if (transferId) {
                    const deletedTransfers = $('#deleted_transfers').val();
                    $('#deleted_transfers').val(deletedTransfers ? `${deletedTransfers},${transferId}` : transferId);
                }
                $(this).closest('.transfer-row').remove();
                calculateRemainingAndCashEquivalent();
            });

            // Track deleted expenses
            $('#expense_fields').on('click', '.remove-expense-btn', function() {
                const expenseId = $(this).data('id');
                if (expenseId) {
                    const deletedExpenses = $('#deleted_expenses').val();
                    $('#deleted_expenses').val(deletedExpenses ? `${deletedExpenses},${expenseId}` : expenseId);
                }
                $(this).closest('.expense-row').remove();
                calculateRemainingAndCashEquivalent();
            });

            // Add Transfer Row
            $('.add-transfer-btn').on('click', function() {
                const transferHtml = `
                    <div class="flex items-center space-x-2 mb-2 transfer-row">
                        <input type="text" class="form-input border rounded px-3" placeholder="Transfer Method" style="width:20%;" name="new_transfer_keys[]" required>
                        <span>-</span>
                        <input type="number" class="form-input border rounded px-3 transfer-value" placeholder="Transfer Value" style="width:15%;" name="new_transfer_values[]" required>
                        <input type="file" class="form-input border rounded px-3" style="width:35%;" name="new_transfer_images[]">
                        <button type="button" class="bg-gray-500 remove-transfer-btn text-red-500 font-semibold px-2 rounded">-</button>
                    </div>`;
                $('#transfer_fields').append(transferHtml);
            });

            // Add Expense Row
            $('.add-expense-btn').on('click', function() {
                const expenseHtml = `
                    <div class="flex items-center space-x-2 mb-2 expense-row">
                        <input type="text" class="form-input border rounded px-3" placeholder="Expense Name" style="width:45%;" name="new_expense_keys[]" required>
                        <span>-</span>
                        <input type="number" class="form-input border rounded px-3 expense-value" placeholder="Expense Value" style="width:45%;" name="new_expense_values[]" required>
                        <button type="button" class="bg-gray-500 remove-expense-btn text-red-500 font-semibold px-2 rounded">-</button>
                    </div>`;
                $('#expense_fields').append(expenseHtml);
            });

            // Submit the form using AJAX
            $('#updateTransactionBtn').on('click', function(e) {
                e.preventDefault();
                const formData = new FormData($('#transactionUpdateForm')[0]);
                $.ajax({
                    url: "{{ route('transactions.update', $transaction->id) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert('Transaction updated successfully!');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while updating the transaction.');
                        console.log(xhr.responseText);
                    }
                });
            });

            function calculateCoinTotal() {
                const coin200 = parseInt($('#coin_200').val()) || 0;
                const coin100 = parseInt($('#coin_100').val()) || 0;
                const coin50 = parseInt($('#coin_50').val()) || 0;
                const coin20 = parseInt($('#coin_20').val()) || 0;
                const coin10 = parseInt($('#coin_10').val()) || 0;
                const coin5 = parseInt($('#coin_5').val()) || 0;
                const coin1 = parseInt($('#coin_1').val()) || 0;

                const total =
                coin200 * 200 +
                coin100 * 100 +
                coin50 * 50 +
                coin20 * 20 +
                coin10 * 10 +
                coin5 * 5 +
                coin1 * 1;

                $('#coin_total').val(total);

                const cashEquivalent = parseFloat($('#cash_equivalent').val()) || 0;

                if (total === cashEquivalent) {
                $('button[type="submit"]').prop('disabled', false).addClass('btn-enabled-animation');
                $('#match-indicator').removeClass('hidden').addClass('fade-in');
                } else {
                $('button[type="submit"]').prop('disabled', true).removeClass('btn-enabled-animation');
                $('#match-indicator').addClass('hidden').removeClass('fade-in');
                }
            }

            $('#coin_200, #coin_100, #coin_50, #coin_20, #coin_10, #coin_5, #coin_1, #cash_equivalent').on('keyup change', calculateCoinTotal);

            // Initial calculation
            calculateCoinTotal();
            calculateRemainingAndCashEquivalent();
        });
    </script>
</x-app-layout>
