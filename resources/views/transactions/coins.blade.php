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
                        <h2 class="text-2xl font-bold mb-6">Validate Currency Details</h2>
                        
                        <!-- Coins Table -->
                        <div class="section-wrapper coins-section">
                            <table id="coinsValidationTable" class="table-auto border-collapse border border-gray-300 w-full text-center">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th>Denomination</th>
                                        <th>Total Count (DB)</th>
                                        <th>Enter Count</th>
                                        <th>Validation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $coinValues = [
                                            '200 LE' => $totalCoins['coin_200'] ?? 0,
                                            '100 LE' => $totalCoins['coin_100'] ?? 0,
                                            '50 LE' => $totalCoins['coin_50'] ?? 0,
                                            '20 LE' => $totalCoins['coin_20'] ?? 0,
                                            '10 LE' => $totalCoins['coin_10'] ?? 0,
                                            '1 LE' => $totalCoins['coin_1'] ?? 0,
                                        ];
                                    @endphp

                                    @foreach ($coinValues as $denomination => $dbCount)
                                        <tr>
                                            <td>{{ $denomination }}</td>
                                            <td class="font-bold">{{ $dbCount }}</td>
                                            <td>
                                                <input type="number" class="form-input border rounded px-3 validate-input" data-db-count="{{ $dbCount }}" min="0" />
                                            </td>
                                            <td class="validation-status text-gray-600"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Money Shortage -->
                        <div class="mt-6">
                            <label for="money_shortage" class="block font-bold">Money Shortage:</label>
                            <input type="text" id="money_shortage" name="money_shortage" class="form-input border rounded px-3" readonly value="{{ $moneyShortage }}" />
                        </div>

                        <!-- Done Button (Hidden Initially) -->
                        <div id="doneButtonContainer" class="mt-6 hidden">
                            <a href="{{ route('transaction.index') }}" id="doneButton" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Done</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Function to validate all rows
            function validateAllRows() {
                let allValid = true;

                $('#coinsValidationTable tbody tr').each(function () {
                    const enteredValue = parseInt($(this).find('.validate-input').val()) || 0; // Get entered value
                    const dbValue = parseInt($(this).find('.validate-input').data('db-count')); // Get DB value

                    const validationStatusCell = $(this).find('.validation-status');

                    // Check for matching values
                    if (enteredValue === dbValue) {
                        validationStatusCell.text('✅ Matching')
                            .removeClass('text-gray-500')
                            .addClass('text-gray-500');
                        $(this).css('background-color', '#d3d3d3'); // Set background to gray
                    } else {
                        validationStatusCell.text('❌ Mismatch')
                            .removeClass('text-gray-500')
                            .addClass('text-gray-500');
                        $(this).css('background-color', ''); // Reset background
                        allValid = false; // Mark as invalid
                    }
                });

                // Show/hide "Done" button
                if (allValid) {
                    $('#doneButtonContainer').removeClass('hidden');
                } else {
                    $('#doneButtonContainer').addClass('hidden');
                }
            }

            // Trigger validation on keyup
            $('.validate-input').on('keyup', function () {
                validateAllRows(); // Revalidate all rows
            });

            // Initial validation check
            validateAllRows();
        });
    </script>
</x-app-layout>
