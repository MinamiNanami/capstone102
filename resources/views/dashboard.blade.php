@extends('layouts.default-layout')

@section('content')
<div class="w-full p-4 ">
    <div class="bg-gray-100 font-roboto">
        <div class="container mx-auto p-4">

            <h1 class="text-3xl font-bold mb-4">Analytics Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Daily Sale Card -->
                <div
                    id="dailySaleCard"
                    class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between cursor-pointer hover:bg-gray-50 transition">
                    <div>
                        <h2 class="text-xl font-bold mb-4">Daily Sale</h2>
                        <p class="text-gray-700 text-2xl">
                            ₱ {{ $totalDailySales }}
                            @if($totalDailySales > $totalPreviousDaySales)
                            <i class="fas fa-arrow-up text-green-500 ml-2"></i>
                            @else
                            <i class="fas fa-arrow-down text-red-500 ml-2"></i>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mt-6 mb-4">Daily Clients</h2>
                        <p class="text-gray-700 text-2xl">
                            <i class="fas fa-user mr-2"></i>
                            {{ $totalDailyClients }}
                        </p>
                    </div>
                </div>

                <!-- Nearest Upcoming Schedule Card (Table Style) -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    @php
                    use Illuminate\Support\Carbon;

                    $nextSchedule = $schedules
                    ->filter(fn($s) => Carbon::parse($s->date)->isFuture())
                    ->sortBy('date')
                    ->first();
                    @endphp

                    <h2 class="text-xl font-bold mb-4">Upcoming Appointment</h2>

                    @if ($nextSchedule)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 font-semibold text-gray-900">
                                <tr>
                                    <th class="px-4 py-2">Title</th>
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $nextSchedule->title }}</td>
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($nextSchedule->date)->format('F j, Y') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $nextSchedule->time ? \Carbon\Carbon::parse($nextSchedule->time)->format('g:i A') : 'N/A' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">No upcoming schedules.</p>
                    @endif
                </div>

                @include('partials.analytics-charts')
            </div>

            @include('partials.sales-and-clients-charts')
        </div>
    </div>
</div>

<!-- Transactions Modal (if needed for Daily) -->
<div id="transactionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50 overflow-auto p-4">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto relative">
        <button onclick="closeTransactionModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-3xl font-bold leading-none">&times;</button>
        <h2 id="transactionsModalTitle" class="text-2xl font-bold mb-4"></h2>
        <div id="transactionsList" class="space-y-2 max-h-[70vh] overflow-auto"></div>
    </div>
</div>

<script>
    // Utility to format date nicely
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
            document.getElementById('transactionsList').innerHTML = `<p class="text-gray-500">No transactions found.</p>`;
            showModal();
            return;
        }

        let totalAllTransactions = 0;
        let html = '';

        transactions.forEach(tx => {
            const createdAt = formatDate(tx.created_at);
            let totalPrice = 0;
            let itemsString = '';

            if (tx.items && tx.items.length > 0) {
                itemsString = tx.items.map(item => `${item.quantity}x ${item.item_name} - ₱${parseFloat(item.price).toFixed(2)}`).join('; ');
                totalPrice = tx.items.reduce((sum, item) => sum + parseFloat(item.price), 0);
            }

            totalAllTransactions += totalPrice;

            html += `
                <div 
                    class="bg-gray-100 rounded shadow p-3 border flex justify-between items-center cursor-pointer hover:bg-gray-200"
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

        html += `
            <div class="mt-4 border-t pt-4 text-right font-bold text-xl">
                Total of All Transactions: ₱${totalAllTransactions.toFixed(2)}
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

    function showReceiptModal(el) {
        const date = el.dataset.date;
        const customer = el.dataset.name;
        const itemsString = el.dataset.items || '';
        const total = el.dataset.total || '0.00';

        const itemsArray = itemsString.split(';').map(line => line.trim()).filter(line => line);

        let receiptTable = `
            <table class="w-full text-sm border-t border-b mb-2">
                <thead>
                    <tr>
                        <th class="text-left py-1">QTY</th>
                        <th class="text-left py-1">ITEM</th>
                        <th class="text-right py-1">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
        `;

        itemsArray.forEach(line => {
            const match = line.match(/^(\d+)x\s(.+?)\s-\s₱([\d,.]+)/);
            if (match) {
                const qty = parseInt(match[1]);
                const name = match[2];
                const itemTotal = parseFloat(match[3].replace(/,/g, ''));
                receiptTable += `
                    <tr>
                        <td class="py-1">${qty}</td>
                        <td class="py-1">${name}</td>
                        <td class="py-1 text-right">₱${itemTotal.toFixed(2)}</td>
                    </tr>
                `;
            }
        });

        receiptTable += `
                </tbody>
            </table>
        `;

        alert(
            `Transaction Receipt\n\n` +
            `Date: ${date}\n` +
            `Customer: ${customer}\n\n` +
            `Items:\n` +
            itemsArray.map(line => '- ' + line).join('\n') + `\n\n` +
            `Total: ₱${total}`
        );
    }

    // ✅ Only for Daily
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