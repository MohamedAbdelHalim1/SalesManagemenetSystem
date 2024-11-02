<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">User Reports</h3>

                <!-- Date Range Filter -->
                <div class="flex gap-4 mb-4">
                    <div>
                        <label for="openDay" class="block font-semibold mb-1">Open Day</label>
                        <input type="date" id="openDay" class="border rounded px-3 py-2">
                    </div>
                    <div>
                        <label for="closeDay" class="block font-semibold mb-1">Close Day</label>
                        <input type="date" id="closeDay" class="border rounded px-3 py-2">
                    </div>
                    <div class="flex items-end gap-2">
                        <button id="filterBtn" class="filter-btn text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
                        <button id="resetBtn" class="reset-btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Reset</button>
                    </div>
                </div>

                <!-- User Reports Table -->
                <table class="w-full border rounded" id="reportsTable">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4">Report Number</th>
                            <th class="py-2 px-4">Opened At</th>
                            <th class="py-2 px-4">Closed At</th>
                            <th class="py-2 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openCloses as $openClose)
                            <tr class="border-t report-row">
                                <td class="py-2 px-4 text-center">{{ $loop->iteration }}</td>
                                <td class="py-2 px-4 text-center open-date">{{ $openClose->open_at }}</td>
                                <td class="py-2 px-4 text-center close-date">{{ $openClose->close_at ?? 'Open' }}</td>
                                <td class="py-2 px-4 text-center">
                                    <a href="{{ route('reports.show', $openClose->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        Show
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .filter-btn{
            background-color: rgb(172, 90, 250);
        }
        .filter-btn, .reset-btn{
            width: 70px;
            height: 40px;
        }
    </style>
    <!-- JavaScript to Filter by Date Range -->
    <script>
        document.getElementById('filterBtn').addEventListener('click', function() {
            const openDay = document.getElementById('openDay').value;
            const closeDay = document.getElementById('closeDay').value;
            const rows = document.querySelectorAll('.report-row');

            rows.forEach(row => {
                const openDate = row.querySelector('.open-date').textContent.trim();
                const closeDate = row.querySelector('.close-date').textContent.trim();
                let showRow = true;

                if (openDay && new Date(openDate) < new Date(openDay)) {
                    showRow = false;
                }

                if (closeDay && closeDate !== 'Open' && new Date(closeDate) > new Date(closeDay)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            document.getElementById('openDay').value = '';
            document.getElementById('closeDay').value = '';
            const rows = document.querySelectorAll('.report-row');
            rows.forEach(row => {
                row.style.display = '';
            });
        });
    </script>
</x-app-layout>
