@extends('layouts.default-layout')

@section('content')
<div class="p-6">
    {{-- Page Header --}}
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Transaction History</h1>

        {{-- Print / Export Button --}}
        <button
            type="button"
            onclick="printTransactions()"
            class="bg-green-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-700 transition-colors duration-200">
            Print / Export
        </button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter Panel --}}
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Filter Transactions</h2>
                <div class="flex items-center gap-2">
                    <label for="sortTransactions" class="text-xs font-medium text-gray-600">
                        Sort by:
                    </label>
                    <select
                        id="sortTransactions"
                        class="border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-green-300 focus:border-transparent"
                        onchange="filterTransactions()">
                        <option value="newest">Date: Newest → Oldest</option>
                        <option value="oldest">Date: Oldest → Newest</option>
                        <option value="high">Amount: High → Low</option>
                        <option value="low">Amount: Low → High</option>
                    </select>
                </div>
            </div>

            {{-- Filters Form --}}
            <div class="space-y-4">
                {{-- Search by Customer Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Search Customer
                    </label>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Enter customer name..."
                        class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-300 focus:border-transparent"
                        oninput="filterTransactions()">
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date Range
                    </label>
                    <div class="flex gap-3">
                        <input
                            type="date"
                            id="startDate"
                            class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-300 focus:border-transparent"
                            onchange="filterTransactions()">
                        <input
                            type="date"
                            id="endDate"
                            class="border border-gray-300 rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-300 focus:border-transparent"
                            onchange="filterTransactions()">
                    </div>
                </div>

                {{-- Preset Buttons --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @php
                        $presets = [
                            'today'=>'Today','yesterday'=>'Yesterday','this_week'=>'This Week','last_week'=>'Last Week',
                            'this_month'=>'This Month','last_month'=>'Last Month','this_year'=>'This Year','last_year'=>'Last Year'
                        ];
                    @endphp
                    @foreach($presets as $value => $label)
                        <button
                            type="button"
                            class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium hover:bg-green-200 transition-colors duration-200"
                            onclick="applyPreset('{{ $value }}')">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Content --}}
    <div id="transactionsList" class="space-y-3">
        @foreach($transactions as $transaction)
        <div
            class="transaction-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 cursor-pointer hover:shadow-md hover:border-green-300 transition-all duration-200"
            onclick="showReceipt(this)"
            data-name="{{ $transaction->customer_name }}"
            data-date="{{ $transaction->created_at->format('Y-m-d H:i:s') }}"
            data-display-date="{{ $transaction->created_at->format('F j, Y h:i A') }}"
            data-items="@foreach($transaction->items as $item){{ $item->quantity }}x {{ $item->item_name }} - ₱{{ number_format($item->price, 2) }}@if(!$loop->last)<br>@endif @endforeach"
            data-amount="₱{{ number_format($transaction->items->sum('price'), 2) }}">
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">{{ $transaction->created_at->format('F j, Y h:i A') }}</p>
                    <p class="text-md font-semibold text-gray-800">{{ $transaction->customer_name }}</p>
                </div>
                <div class="text-lg font-bold text-green-600">₱{{ number_format($transaction->items->sum('price'), 2) }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Receipt Modal --}}
<div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl w-11/12 md:w-1/3 max-w-md mx-4 relative">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Transaction Receipt</h2>
            <button onclick="closeModal('receiptModal')" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none">&times;</button>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <div><span class="font-semibold text-gray-700">Customer:</span> <span id="modalCustomer" class="text-gray-900"></span></div>
                <div><span class="font-semibold text-gray-700">Date:</span> <span id="modalDate" class="text-gray-900"></span></div>
                <div>
                    <span class="font-semibold text-gray-700">Items:</span>
                    <div id="modalItems" class="ml-4 mt-2 text-sm text-gray-700 bg-gray-50 p-3 rounded"></div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="text-right"><span class="text-lg font-bold text-green-600">Total: <span id="modalAmount"></span></span></div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    function showReceipt(element) {
        document.getElementById('modalCustomer').textContent = element.dataset.name;
        document.getElementById('modalDate').textContent = element.dataset.displayDate;
        document.getElementById('modalItems').innerHTML = element.dataset.items;
        document.getElementById('modalAmount').textContent = element.dataset.amount;

        const modal = document.getElementById('receiptModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'items-center', 'justify-center');
    }

    document.getElementById('receiptModal').addEventListener('click', function(event) {
        if(event.target === this) closeModal('receiptModal');
    });

    // Filter transactions
    function filterTransactions() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const sort = document.getElementById('sortTransactions').value;

        const transactions = Array.from(document.querySelectorAll('#transactionsList .transaction-card'));

        transactions.forEach(tr => {
            const name = tr.dataset.name.toLowerCase();
            const date = tr.dataset.date;
            let show = true;

            if(search && !name.includes(search)) show = false;
            if(start && new Date(date) < new Date(start)) show = false;
            if(end && new Date(date) > new Date(end + "T23:59:59")) show = false;

            tr.style.display = show ? 'block' : 'none';
        });

        // Sorting
        const list = document.getElementById('transactionsList');
        const sorted = transactions
            .filter(tr => tr.style.display !== 'none')
            .sort((a,b) => {
                const aDate = new Date(a.dataset.date);
                const bDate = new Date(b.dataset.date);
                const aAmount = parseFloat(a.dataset.amount.replace('₱','').replace(',',''));
                const bAmount = parseFloat(b.dataset.amount.replace('₱','').replace(',',''));

                switch(sort) {
                    case 'newest': return bDate - aDate;
                    case 'oldest': return aDate - bDate;
                    case 'high': return bAmount - aAmount;
                    case 'low': return aAmount - bAmount;
                }
            });
        sorted.forEach(tr => list.appendChild(tr));
    }

    function applyPreset(preset) {
        const today = new Date();
        let start, end;

        switch(preset) {
            case 'today':
                start = end = today; break;
            case 'yesterday':
                start = end = new Date(today.setDate(today.getDate()-1)); break;
            case 'this_week':
                start = new Date(today.setDate(today.getDate() - today.getDay()));
                end = new Date(today.setDate(today.getDate() + 6 - today.getDay())); break;
            case 'last_week':
                start = new Date(today.setDate(today.getDate() - today.getDay() - 7));
                end = new Date(today.setDate(today.getDate() + 6 - today.getDay())); break;
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth()+1, 0); break;
            case 'last_month':
                start = new Date(today.getFullYear(), today.getMonth()-1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0); break;
            case 'this_year':
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date(today.getFullYear(), 11, 31); break;
            case 'last_year':
                start = new Date(today.getFullYear()-1, 0, 1);
                end = new Date(today.getFullYear()-1, 11, 31); break;
        }

        document.getElementById('startDate').value = start.toISOString().split('T')[0];
        document.getElementById('endDate').value = end.toISOString().split('T')[0];
        filterTransactions();
    }

    // Print/export
    function printTransactions() {
        const transactions = document.querySelectorAll('#transactionsList .transaction-card');
        let tableRows = '';
        transactions.forEach(tr => {
            if(tr.style.display === 'none') return;
            const items = tr.dataset.items.replace(/<br>/g, ', ');
            const amount = tr.dataset.amount;
            tableRows += `<tr>
                <td style="border:1px solid #ccc;padding:8px;">${tr.dataset.name}</td>
                <td style="border:1px solid #ccc;padding:8px;">${tr.dataset.displayDate}</td>
                <td style="border:1px solid #ccc;padding:8px;">${items}</td>
                <td style="border:1px solid #ccc;padding:8px;text-align:right;">${amount}</td>
            </tr>`;
        });

        const printWindow = window.open('', '', 'width=1000,height=700');
        printWindow.document.write(`
            <html><head><title>Transaction History</title>
            <style>
                body { font-family: sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ccc; padding: 8px; }
                th { background-color: #f3f4f6; text-align: left; }
                h2 { text-align: center; }
            </style>
            </head><body>
            <h2>Transaction History</h2>
            <table>
                <thead>
                    <tr><th>Customer</th><th>Date</th><th>Items</th><th>Total</th></tr>
                </thead>
                <tbody>${tableRows}</tbody>
            </table>
            </body></html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
</script>
@endsection
