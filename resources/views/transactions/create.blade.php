<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="container mx-auto" style="padding:15px;">
                    <!-- User Search Section -->
                    <div class="mb-4">
                        <label for="user_search" class="block font-semibold mb-2">Search by User Name - أسم المندوب</label>
                        <div class="flex gap-2">
                            <input type="text" class="search-input w-full border rounded px-3 py-2" id="user_search_value" placeholder="Enter User Name">
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loading-spinner" class="loading-spinner"></div>

                    <!-- User Preview Table (Initially hidden) -->
                    <div id="user_preview" class="mt-6 hidden">
                        <div class="bg-blue-500 text-white font-semibold p-3 rounded-t">
                            <h5>Matching Users</h5>
                        </div>
                        <table class="w-full border rounded-b overflow-hidden">
                            <thead>
                                <tr class="bg-gray-100 text-left text-gray-600">
                                    <th class="py-2 px-4">User</th>
                                    <th class="py-2 px-4">Email</th>
                                </tr>
                            </thead>
                            <tbody id="user_preview_list" class="bg-white">
                                <!-- Rows of users will be appended here dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- User Information Preview (Initially hidden) -->
                    <div id="user-info" class="mt-8 hidden">
                        <h4 class="text-xl font-bold mb-3">User Information</h4>
                        <table class="w-full border rounded">
                            <thead>
                                <tr class="bg-gray-100 text-left text-gray-600">
                                    <th class="py-2 px-4">User ID</th>
                                    <th class="py-2 px-4">Name</th>
                                    <th class="py-2 px-4">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="user_id_info" class="py-2 px-4 text-center"></td>
                                    <td id="user_name_info" class="py-2 px-4 text-center"></td>
                                    <td id="user_email_info" class="py-2 px-4 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Form for Transaction Creation -->
                    <form id="transactionForm" action="{{ route('transactions.store') }}" method="POST" class="hidden mt-6" enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden input to store the selected user_id -->
                        <input type="hidden" id="user_id" name="user_id">
                        <input type="hidden" id="hidden_cash_equivalent" name="cash_equivalent" value="0">


                        <!-- Transaction Details -->
                        <div class="mb-4" style="display:flex;flex-direction:row;justify-content:space-between;width:100%;border-bottom:1px solid #ddd;padding:15px;">
                            <div style="display:flex;flex-direction:column;width:100%;">
                                <label for="reference_collection" class="block font-semibold mb-2">Reference Collection - عمليه التحصيل</label>
                                <input type="number" class="form-input border rounded px-3" id="reference_collection" name="reference_collection" placeholder="Enter Reference Collection" style="width:50%;">
                                <label for="order_number" class="block font-semibold mb-2">Number Of Orders - عدد الاوردرات</label>
                                <input type="number" class="form-input border rounded px-3" id="order_number" name="order_number" placeholder="Enter Number Of Orders" style="width:50%;">
                                <label for="order_delivered" class="block font-semibold mb-2">Orders Delivered - تم التسليم</label>
                                <input type="number" class="form-input border rounded px-3" id="order_delivered" name="order_delivered" placeholder="Enter Orders Delivered" style="width:50%;">
                            </div>
                            <div style="display:flex;flex-direction:column;width:100%;">
                                <label for="total" class="block font-semibold mb-2">Total Collection - اجمالي التحصيل</label>
                                <input type="number" class="form-input border rounded px-3" id="total" name="total_collection" placeholder="Enter Total Collection" style="width:50%;">
                                <label for="commission" class="block font-semibold mb-2">Commission Value - عمولة المندوب</label>
                                <input type="number" class="form-input border rounded px-3" id="commission" name="commission_value" placeholder="Enter Your Commission" style="width:50%;">
                                <label for="remaining" class="block font-semibold mb-2">Total Remaining - المتبقي بعد خصم العموله</label>
                                <input type="number" class="form-input border rounded px-3 transition-all duration-200 ease-in-out" id="remaining" name="remaining" style="width:50%;" readonly>
                            </div>
                        </div>

                        <!-- Any Transfer Section -->
                        <!-- Transfer Section -->
                        <div class="mb-4" style="display:flex; flex-direction:column; border-bottom:1px solid #ddd; padding:15px;">
                            <label for="transfers" class="block font-semibold mb-2">Any Transfers...? التحويلات</label>
                            <div id="transfer_fields">
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" class="form-input border rounded px-3" placeholder="Transfer Method" style="width:25%;" name="transfer_keys[]">
                                    <span>-</span>
                                    <input type="number" class="form-input border rounded px-3" placeholder="Transfer Value" style="width:20%;" name="transfer_values[]">
                                    <input type="file" class="form-input border rounded px-3" style="width:35%;" name="transfer_images[]">
                                    <button type="button" class="add-transfer-btn text-white font-semibold px-2 rounded hover:bg-blue-700">+</button>
                                </div>
                            </div>                            
                        </div>


                        <!-- Any Expenses Section -->
                        <div class="mb-4" style="display:flex; flex-direction:column; border-bottom:1px solid #ddd; padding:15px;">
                            <label for="expenses" class="block font-semibold mb-2">Any Expenses...? المصاريف</label>
                            <div id="expense_fields">
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="text" class="form-input border rounded px-3" placeholder="Expense Name" style="width:45%;" name="expense_keys[]">
                                    <span>-</span>
                                    <input type="number" class="form-input border rounded px-3" placeholder="Expense Value" style="width:45%;" name="expense_values[]">
                                    <button type="button" class="add-expense-btn text-white font-semibold px-2 rounded hover:bg-blue-700">+</button>
                                </div>
                            </div>
                        </div>

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
                                        <th class="py-2 px-4">Total</th>
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
                                        <td>
                                            <input type="number" id="coin_total" name="coin_total" class="form-input border rounded px-3 text-center" value="0" style="width: 80px;" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Submit Button and Cash Equivalent Calculation -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center">
                                <button type="submit" class="search text-white font-semibold px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50" disabled>
                                    Submit Transaction
                                </button>
                                <div id="match-indicator" class="hidden ml-3 text-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="text-right">
                                <h2 class="text-lg font-semibold">Your cash should equal</h2>
                                <input type="number" id="cash_equivalent" class="form-input border rounded px-3 transition-all duration-200 ease-in-out mt-2 text-center" style="width:150px;" name="total_cash" readonly>
                            </div>
                        </div>                        
                    </form>

                </div>
            </div>
        </div>
    </div>
    <style>
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            display: none;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .search {
            background-color:#0d6efd;
        }
        .add-expense-btn {
            background-color:#0d6efd;
        }
        .add-transfer-btn {
            background-color:#0d6efd;
        }
        .form-input {
            width:35%;
        }
        #remaining.blink, #cash_equivalent.blink {
            animation: blink 0.5s ease-in-out;
        }
        @keyframes blink {
            0% { background-color: #f0f0f0; }
            50% { background-color: #e0f7fa; }
            100% { background-color: #f0f0f0; }
        }

        .btn-enabled-animation {
            animation: highlight 1s ease-in-out infinite;
        }

        @keyframes highlight {
            0% { background-color: #0d6efd; }
            50% { background-color: #5cdb95; }
            100% { background-color: #0d6efd; }
        }

        .fade-in {
            animation: fade-in 0.5s ease-in-out forwards;
        }

        @keyframes fade-in {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }

    </style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {

        function calculateCashEquivalent() {
            const remaining = parseFloat($('#remaining').val()) || 0;
            let transferTotal = 0;
            let expenseTotal = 0;

            $('input[name^="transfer_values"]').each(function () {
                transferTotal += parseFloat($(this).val()) || 0;
            });

            $('input[name="expense_values[]"]').each(function () {
                expenseTotal += parseFloat($(this).val()) || 0;
            });

            const cashEquivalent = remaining - transferTotal - expenseTotal;
            $('#cash_equivalent').val(cashEquivalent.toFixed(2)).addClass('blink');
            $('#hidden_cash_equivalent').val(cashEquivalent.toFixed(2)); // Update hidden input field
            setTimeout(() => $('#cash_equivalent').removeClass('blink'), 500);

            calculateCoinTotal(); // Trigger coin total check whenever cash equivalent is recalculated
        }



        $('#total, #commission').on('keyup', function() {
            const totalCollection = parseFloat($('#total').val()) || 0;
            const commission = parseFloat($('#commission').val()) || 0;
            const remaining = totalCollection - commission;

            $('#remaining').val(remaining.toFixed(2)).addClass('blink');
            setTimeout(() => $('#remaining').removeClass('blink'), 500);

            calculateCashEquivalent();
        });

        $(document).on('keyup', 'input[name^="transfer_values"], input[name="expense_values[]"]', calculateCashEquivalent);


            // Dynamic Transfer Section
        // Dynamic Transfers Section
        $('#transfer_fields').on('click', '.add-transfer-btn', function() {
            const transferHtml = `
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" class="form-input border rounded px-3" placeholder="Transfer Method" style="width:25%;" name="transfer_keys[]">
                    <span>-</span>
                    <input type="number" class="form-input border rounded px-3" placeholder="Transfer Value" style="width:20%;" name="transfer_values[]">
                    <input type="file" class="form-input border rounded px-3" style="width:35%;" style="width:35%;" name="transfer_images[]">
                    <button type="button" class="add-transfer-btn text-white font-semibold px-2 rounded hover:bg-blue-700">+</button>
                </div>`;
            $('#transfer_fields').append(transferHtml);
            $(this).remove(); // Remove the old "+" button once a new row is added
        });

        // Dynamic Expenses Section
        $('#expense_fields').on('click', '.add-expense-btn', function() {
            const expenseHtml = `
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" class="form-input border rounded px-3" placeholder="Expense Name" style="width:45%;" name="expense_keys[]">
                    <span>-</span>
                    <input type="number" class="form-input border rounded px-3" placeholder="Expense Value" style="width:45%;" name="expense_values[]">
                    <button type="button" class="add-expense-btn text-white font-semibold px-2 rounded hover:bg-blue-700">+</button>
                </div>`;
            $('#expense_fields').append(expenseHtml);
            $(this).remove(); // Remove the old '+' button once a new row is added
        });



        $('#total, #commission').on('keyup', function() {
            const totalCollection = parseFloat($('#total').val()) || 0;
            const commission = parseFloat($('#commission').val()) || 0;
            const remaining = totalCollection - commission;

            $('#remaining').val(remaining.toFixed(2));  // Update field value with calculated remaining
            $('#remaining').addClass('blink');          // Add animation class to highlight field
            setTimeout(() => $('#remaining').removeClass('blink'), 500); // Remove animation class after highlight
        });


        // Search User by Name
        $('#user_search_value').on('keyup', function() {
            const query = $(this).val();
                if (query.length > 0) { 
                    searchUser(query);
                }
        });

        // AJAX Request for User Search
        function searchUser(query) {
            $('#loading-spinner').show();  // Show spinner while searching

            $.ajax({
                url: '/search-user',
                type: 'GET',
                data: { query: query },
                success: function(response) {
                    $('#loading-spinner').hide(); // Hide spinner after success
                    if (response.length > 0) {
                        $('#user_preview_list').empty();
                        displayUsers(response);
                    } else {
                        $('#user_preview_list').empty();
                        $('#user_preview').hide();
                        alert('No Matching users. Please try again.');

                    }
                },
                error: function() {
                    $('#loading-spinner').hide();
                    alert('Error searching for users. Please try again.');
                }
            });
        }

        // Display Users in Table
        function displayUsers(users) {
            $('#user_preview_list').empty();  // Clear old results

            users.forEach(user => {
                const userRow = `
                    <tr class="user-row cursor-pointer hover:bg-gray-100" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}">
                        <td class="py-2 px-4 text-center">${user.name}</td>
                        <td class="py-2 px-4 text-center">${user.email}</td>
                    </tr>`;
                $('#user_preview_list').append(userRow);
            });

            $('#user_preview').show();
        }

        // Handle User Selection
        $(document).on('click', '.user-row', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const userEmail = $(this).data('email');
            
            $('#user_search_value').val(userName);
            $('#user_id').val(userId);
            
            displayUserInfo(userId, userName, userEmail);
        });

        // Display Selected User Information
        function displayUserInfo(userId, userName, userEmail) {
            $('#user_id_info').text(userId);
            $('#user_name_info').text(userName);
            $('#user_email_info').text(userEmail);

            $('#user-info').show();
            $('#transactionForm').show();
            $('#user_preview').hide();
        }

        // Submit the Transaction form via AJAX
        $('#submitTransaction').on('click', function() {
            const userId = $('#user_id').val();
            const transactionAmount = $('#transaction_amount').val();

            $.ajax({
                url: '/transactions',
                type: 'POST',
                data: {
                    user_id: userId,
                    transaction_amount: transactionAmount,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    alert('Transaction created successfully');
                    window.location.reload();
                },
                error: function() {
                    alert('Error creating transaction. Please try again.');
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

        // Trigger calculation on input changes
        $('#coin_200, #coin_100, #coin_50, #coin_20, #coin_10, #coin_5, #coin_1, #cash_equivalent').on('keyup change', calculateCoinTotal);

        });
</script>

</x-app-layout>
