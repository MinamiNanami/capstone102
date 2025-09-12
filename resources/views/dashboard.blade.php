@extends('layouts.default-layout')

@section('content')
<div class="w-full p-4">
    <div class="bg-gray-100 font-roboto">
        <div class="container mx-auto p-4">

            <h1 class="text-3xl font-bold mb-4">Analytics Dashboard</h1>

            {{-- Main Dashboard Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Daily Sale / Daily Clients / Expiring Items --}}
                <div
                    id="dailySaleCard"
                    class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between cursor-pointer hover:bg-gray-50 transition">

                    <div>
                        <h2 class="text-xl font-bold mb-4">Daily Sale</h2>
                        <p class="text-gray-700 text-4xl">
                            ₱ {{ $totalDailySales }}
                            @if($totalDailySales > $totalPreviousDaySales)
                            <i class="fas fa-arrow-up text-green-500 ml-2"></i>
                            @else
                            <i class="fas fa-arrow-down text-red-500 ml-2"></i>
                            @endif
                        </p>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-xl font-bold mb-4">Daily Clients</h2>
                        <p class="text-gray-700 text-4xl">
                            <i class="fas fa-user mr-2"></i>
                            {{ $totalDailyClients }}
                        </p>
                    </div>

                    {{-- Expiring Inventory Items inside the card --}}
                    <div class="mt-6">
                        <h2 class="text-xl font-bold mb-3">Expiring Inventory Items (Next 30 Days)</h2>
                        @if($expiringItems->isNotEmpty())
                        <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                            @foreach($expiringItems as $item)
                            <div class="bg-yellow-100 p-2 rounded shadow flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">{{ $item->name }}</p>
                                    <p class="text-sm text-gray-700">Qty: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-sm font-semibold text-red-600">
                                    Exp: {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-sm">No items expiring in the next 30 days.</p>
                        @endif
                    </div>
                </div>

                {{-- Today's Appointments --}}
                <div class="bg-white p-6 rounded-lg shadow-md">
                    @php
                    use Illuminate\Support\Carbon;

                    $todaySchedules = $schedules
                    ->filter(fn($s) => Carbon::parse($s->date)->isToday())
                    ->sortBy('time');
                    @endphp

                    <h2 class="text-xl font-bold mb-4">Today's Appointments</h2>

                    @if ($todaySchedules->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 font-semibold text-gray-900">
                                <tr>
                                    <th class="px-4 py-2">Title</th>
                                    <th class="px-4 py-2">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todaySchedules as $schedule)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $schedule->title }}</td>
                                    <td class="px-4 py-2">
                                        {{ $schedule->time ? Carbon::parse($schedule->time)->format('g:i A') : 'N/A' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">No appointments today.</p>
                    @endif
                </div>

                @include('partials.analytics-charts')
            </div>

            @include('partials.sales-and-clients-charts')
        </div>
    </div>
</div>

<div id="transactionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 overflow-auto p-4">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto relative">
        <button onclick="closeTransactionModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-3xl font-bold leading-none">&times;</button>
        <h2 id="transactionsModalTitle" class="text-2xl font-bold mb-4"></h2>
        <div id="transactionsList" class="space-y-2 max-h-[70vh] overflow-auto"></div>
    </div>
</div>

<script>
    function formatDate(dateString) {
        const d = new Date(dateString);
        return d.toLocaleString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        });
    }

    function renderTransactions(title, transactions) {
        document.getElementById('transactionsModalTitle').textContent = title;

        if (!transactions.length) {
            document.getElementById('transactionsList').innerHTML =
                `<p class="text-gray-500">No transactions found.</p>`;
            showModal();
            return;
        }

        let totalAllTransactions = 0;
        const overallItems = {};
        let html = '';

        transactions.forEach(tx => {
            const createdAt = formatDate(tx.created_at);
            let totalPrice = 0;
            let itemsString = '';

            if (tx.items && tx.items.length > 0) {
                itemsString = tx.items.map(item => `${item.quantity}x ${item.item_name} - ₱${parseFloat(item.price).toFixed(2)}`).join('; ');
                totalPrice = tx.items.reduce((sum, item) => sum + parseFloat(item.price), 0);

                tx.items.forEach(item => {
                    if (!overallItems[item.item_name]) overallItems[item.item_name] = 0;
                    overallItems[item.item_name] += parseInt(item.quantity);
                });
            }

            totalAllTransactions += totalPrice;

            html += `
                <div 
                    class="bg-gray-100 rounded shadow p-3 border flex justify-between items-center cursor-pointer hover:bg-gray-200 mb-2"
                    onclick="showReceiptModal(this)"
                    data-name="${tx.customer_name}"
                    data-date="${createdAt}"
                    data-items="${itemsString}"
                    data-total="${totalPrice.toFixed(2)}"
                >
                    <div>
                        <p class="text-sm text-gray-600">${createdAt}</p>
                        <p class="text-md font-medium">${tx.customer_name}</p>
                    </div>
                    <div class="text-md font-bold text-green-600">₱${totalPrice.toFixed(2)}</div>
                </div>
            `;
        });

        // Modern Overall Items Sold table
        html += `
            <div class="mt-6">
                <h3 class="font-bold text-lg mb-2">Overall Items Sold:</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 text-gray-700">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left">Item Name</th>
                                <th class="px-4 py-2 text-right">Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Object.entries(overallItems).map(([itemName, qty]) => `
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">${itemName}</td>
                                    <td class="px-4 py-2 text-right font-semibold">${qty}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right font-bold text-xl text-green-600">
                    Total of All Transactions: ₱${totalAllTransactions.toFixed(2)}
                </div>
            </div>
        `;

        document.getElementById('transactionsList').innerHTML = html;
        showModal();
    }

    function showModal() {
        const modal = document.getElementById('transactionsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeTransactionModal() {
        const modal = document.getElementById('transactionsModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.getElementById('dailySaleCard').addEventListener('click', () => {
        fetch('/api/transactions/daily')
            .then(res => res.json())
            .then(data => {
                renderTransactions('Daily Transactions', data.transactions);
            })
            .catch(() => {
                alert('Failed to load daily transactions.');
            });
    });
</script>
@endsection