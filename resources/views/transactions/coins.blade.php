<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto py-12">
                        <h2 class="text-2xl font-bold mb-6">Add Currency Details</h2>
                        <form action="{{ route('transactions.storeCoins', $open_close_id) }}" method="POST">
                            @csrf
                            <!-- Readonly Total Cash from Method -->
                            <div style="display:flex; flex-direction:row ; gap:15px; padding:20px;">
                                <div>
                                    <label for="total_cash">Total Cash (from method):</label>
                                    <input type="number" id="total_cash" name="total_cash" class="form-input border rounded px-3" readonly value="{{ $total_cash }}" />
                                </div>
    
                                <!-- Readonly Total of All Coins -->
                                <div>
                                    <label for="total_coins">Total of All Coins:</label>
                                    <input type="number" id="total_coins" class="form-input border rounded px-3" readonly />
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>200 LE 
                                        <input type="number" name="coin_200" class="form-input border rounded px-3" min="0" required />
                                    </label>
                                    <label>100 LE 
                                        <input type="number" name="coin_100" class="form-input border rounded px-3" min="0" required />
                                    </label>
                                     <label>50 LE 
                                         <input type="number" name="coin_50" class="form-input border rounded px-3" min="0" required />
                                     </label>
                                </div>
                             </div>

                            <!-- Currency Inputs -->
                            <div class="row">
                                <div class="col-md-3">
                                    <label>20 LE 
                                        <input type="number" name="coin_20" class="form-input border rounded px-3" min="0" required />
                                    </label>
                                    <label>10 LE 
                                        <input type="number" name="coin_10" class="form-input border rounded px-3" min="0" required />
                                    </label>
                                    <label>1 LE 
                                        <input type="number" name="coin_1" class="form-input border rounded px-3" min="0" required />
                                    </label>
                                    <label>0.5 LE 
                                        <input type="number" name="coin_0_5" class="form-input border rounded px-3" min="0" required />
                                    </label>
                                    
                                </div>
                            </div>
                            

                            <!-- Money Shortage Calculation -->
                            <div class="mt-4">
                                <label for="money_shortage">Money Shortage:</label>
                                <input type="text" id="money_shortage" name="money_shortage" class="form-input border rounded px-3" readonly />
                            </div>

                            <button type="submit" class="mt-6 bg-gray-500 text-white px-4 py-2 rounded">Save Currency</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to calculate the total of all coins
            function calculateTotalCoins() {
                const coinValues = {
                    coin_0_5: 0.5,
                    coin_1: 1,
                    coin_10: 10,
                    coin_20: 20,
                    coin_50: 50,
                    coin_100: 100,
                    coin_200: 200
                };

                let totalCoins = 0;
                for (const [coinName, coinValue] of Object.entries(coinValues)) {
                    const coinCount = parseFloat($(`input[name="${coinName}"]`).val()) || 0;
                    totalCoins += coinCount * coinValue;
                }

                // Update the total coins input field
                $('#total_coins').val(totalCoins.toFixed(2));

                // Calculate and display money shortage
                const totalCash = parseFloat($('#total_cash').val()) || 0;
                const moneyShortage = totalCash - totalCoins;
                $('#money_shortage').val(moneyShortage.toFixed(2));
            }

            // Recalculate total coins and money shortage whenever a coin input changes
            $('input[name^="coin_"]').on('input', calculateTotalCoins);

            // Initial calculation on page load
            calculateTotalCoins();
        });
    </script>

</x-app-layout>
