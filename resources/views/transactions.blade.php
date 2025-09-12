@extends('layouts.default-layout')

@section('content')
<div class="p-6 flex flex-col h-screen">
    {{-- Page Header --}}
    <div class="mb-6 flex justify-between items-center flex-shrink-0">
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
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6 flex-shrink-0">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter Panel --}}
    <div class="bg-white rounded-lg shadow-md mb-6 flex-shrink-0">
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
    <div class="flex-1 overflow-auto border border-gray-200 rounded p-2">
        <div id="transactionsList" class="space-y-3">
            @foreach($transactions as $transaction)
            @php
            $totalAmount = $transaction->total ?? $transaction->items->sum(fn($item) => $item->price * $item->quantity);
            @endphp
            <div
                class="transaction-card bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-green-300 transition-all duration-200"
                data-name="{{ $transaction->customer_name }}"
                data-date="{{ $transaction->created_at->format('Y-m-d H:i:s') }}"
                data-display-date="{{ $transaction->created_at->format('F j, Y h:i A') }}"
                data-service="{{ $transaction->service ?? 'N/A' }}"
                data-service-fee="₱{{ number_format($transaction->service_fee ?? 0, 2) }}"
                data-discount="{{ $transaction->discount ?? 0 }}%"
                data-items="@foreach($transaction->items as $item){{ $item->quantity }}x {{ $item->item_name }} - ₱{{ number_format($item->price, 2) }}@if(!$loop->last)<br>@endif @endforeach"
                data-amount="₱{{ number_format($totalAmount, 2) }}">
                <div class="flex justify-between items-center">
                    <div class="flex-1 cursor-pointer" onclick="showReceipt(this.parentElement.parentElement)">
                        <p class="text-sm text-gray-500 mb-1">{{ $transaction->created_at->format('F j, Y h:i A') }}</p>
                        <p class="text-md font-semibold text-gray-800">{{ $transaction->customer_name }}</p>
                        <p class="text-xs text-gray-500">Service: {{ $transaction->service ?? 'N/A' }}</p>
                    </div>
                    <div class="text-lg font-bold text-green-600">₱{{ number_format($totalAmount, 2) }}</div>
                </div>
                <div class="mt-3 flex justify-end">
                    <button
                        onclick="printSingleReceipt(this.closest('.transaction-card'))"
                        class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-blue-700 transition-colors duration-200">
                        Print Receipt
                    </button>
                </div>
            </div>
            @endforeach
        </div>
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
                <div><span class="font-semibold text-gray-700">Service:</span> <span id="modalService" class="text-gray-900"></span></div>
                <div><span class="font-semibold text-gray-700">Service Fee:</span> <span id="modalServiceFee" class="text-gray-900"></span></div>
                <div><span class="font-semibold text-gray-700">Discount:</span> <span id="modalDiscount" class="text-gray-900"></span></div>
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
        document.getElementById('modalService').textContent = element.dataset.service || 'N/A';
        document.getElementById('modalServiceFee').textContent = element.dataset.serviceFee;
        document.getElementById('modalDiscount').textContent = element.dataset.discount;
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
        if (event.target === this) closeModal('receiptModal');
    });

    function printSingleReceipt(transactionCard) {
        const name = transactionCard.dataset.name;
        const date = transactionCard.dataset.displayDate;
        const service = transactionCard.dataset.service;
        const serviceFee = transactionCard.dataset.serviceFee;
        const discount = transactionCard.dataset.discount;
        const itemsRaw = transactionCard.dataset.items;

        const itemsArray = itemsRaw.split('<br>').map(line => {
            const match = line.match(/(\d+)x (.+) - ₱([\d,]+\.\d{2})/);
            if (match) {
                const qty = parseInt(match[1]);
                const itemName = match[2];
                const price = parseFloat(match[3].replace(/,/g, ''));
                return {
                    qty,
                    itemName,
                    subtotal: qty * price
                };
            }
            return null;
        }).filter(Boolean);

        const subtotal = itemsArray.reduce((sum, it) => sum + it.subtotal, 0);
        const finalTotal = subtotal + parseFloat(serviceFee.replace('₱', '').replace(/,/g, '')) - (subtotal * parseFloat(discount) / 100);
        const receiptNumber = "#" + Date.now().toString().slice(-6);

        const printWindow = window.open('', '', 'height=700,width=500');
        printWindow.document.write(`
            <html>
            <head>
                <title>Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; line-height: 1.4; color: #333; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .logo { width: 60px; height: 60px; background-color: #f0f0f0; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px; color: #666; }
                    .clinic-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
                    .clinic-address { font-size: 12px; color: #666; margin-bottom: 2px; }
                    .receipt-number { font-size: 24px; font-weight: bold; text-align: right; margin: 20px 0; }
                    .customer-info { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
                    .items-summary { font-size: 14px; color: #666; margin-bottom: 20px; }
                    .items-list { margin-bottom: 30px; }
                    .item-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
                    .item-qty { font-size: 14px; color: #666; width: 30px; }
                    .item-name { flex: 1; font-size: 14px; margin-left: 10px; }
                    .item-price { font-size: 14px; font-weight: bold; }
                    .totals-section { border-top: 2px solid #333; padding-top: 15px; margin-bottom: 30px; }
                    .total-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
                    .final-total { font-size: 18px; font-weight: bold; border-top: 1px solid #333; padding-top: 10px; margin-top: 10px; }
                    .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
                    .footer-message { font-style: italic; margin-bottom: 10px; color: #666; }
                    .date-time { font-size: 12px; color: #888; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="logo">PVC</div>
                    <div class="clinic-name">Philippians Veterinary Clinic</div>
                    <div class="clinic-address">1234 J.P. Rizal St., Sta Barbara, Bulluag, Bulacan •</div>
                    <div class="clinic-address">0923 020 0442 | 0917 144 0442</div>
                </div>
                <div class="receipt-number">${receiptNumber}</div>
                <div class="customer-info">
                    <div class="customer-name">👤 ${name}</div>
                    <div>Service: ${service}</div>
                    <div>Service Fee: ${serviceFee}</div>
                    <div>Discount: ${discount}</div>
                </div>
                <div class="items-summary">${itemsArray.length} items (Qty.: ${itemsArray.reduce((t,i)=>t+i.qty,0)})</div>
                <div class="items-list">
                    ${itemsArray.map(it => `
                        <div class="item-row">
                            <div class="item-qty">${it.qty}×</div>
                            <div class="item-name">${it.itemName}</div>
                            <div class="item-price">₱${it.subtotal.toFixed(2)}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="totals-section">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>₱${subtotal.toFixed(2)}</span>
                    </div>
                    <div class="total-row">
                        <span>Service Fee:</span>
                        <span>${serviceFee}</span>
                    </div>
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>${discount}</span>
                    </div>
                    <div class="total-row final-total">
                        <span>Total:</span>
                        <span>₱${finalTotal.toFixed(2)}</span>
                    </div>
                </div>
                <div class="footer">
                    <div class="footer-message">Let your pets be our concern too<br>Thanks & God Bless!</div>
                    <div class="date-time">${date}</div>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        };
    }

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

            if (search && !name.includes(search)) show = false;
            if (start && new Date(date) < new Date(start)) show = false;
            if (end && new Date(date) > new Date(end + "T23:59:59")) show = false;

            tr.style.display = show ? 'block' : 'none';
        });

        const list = document.getElementById('transactionsList');
        const sorted = transactions
            .filter(tr => tr.style.display !== 'none')
            .sort((a, b) => {
                const aDate = new Date(a.dataset.date);
                const bDate = new Date(b.dataset.date);
                const aAmount = parseFloat(a.dataset.amount.replace('₱', '').replace(/,/g, ''));
                const bAmount = parseFloat(b.dataset.amount.replace('₱', '').replace(/,/g, ''));

                switch (sort) {
                    case 'newest':
                        return bDate - aDate;
                    case 'oldest':
                        return aDate - bDate;
                    case 'high':
                        return bAmount - aAmount;
                    case 'low':
                        return aAmount - bAmount;
                }
            });
        sorted.forEach(tr => list.appendChild(tr));
    }

    function applyPreset(preset) {
        const today = new Date();
        let start, end;

        switch (preset) {
            case 'today':
                start = end = today;
                break;
            case 'yesterday':
                start = end = new Date(today.setDate(today.getDate() - 1));
                break;
            case 'this_week':
                start = new Date(today.setDate(today.getDate() - today.getDay()));
                end = new Date(today.setDate(today.getDate() + 6 - today.getDay()));
                break;
            case 'last_week':
                start = new Date(today.setDate(today.getDate() - today.getDay() - 7));
                end = new Date(today.setDate(today.getDate() + 6 - today.getDay()));
                break;
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'last_month':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'this_year':
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date(today.getFullYear(), 11, 31);
                break;
            case 'last_year':
                start = new Date(today.getFullYear() - 1, 0, 1);
                end = new Date(today.getFullYear() - 1, 11, 31);
                break;
        }

        document.getElementById('startDate').value = start.toISOString().split('T')[0];
        document.getElementById('endDate').value = end.toISOString().split('T')[0];
        filterTransactions();
    }

    function printTransactions() {
        const transactions = document.querySelectorAll('#transactionsList .transaction-card');
        let tableRows = '';
        transactions.forEach(tr => {
            if (tr.style.display === 'none') return;
            const items = tr.dataset.items.replace(/<br>/g, '\n');
            tableRows += `
                <tr>
                    <td style="padding:6px;border:1px solid #ccc;">${tr.dataset.name}</td>
                    <td style="padding:6px;border:1px solid #ccc;">${tr.dataset.displayDate}</td>
                    <td style="padding:6px;border:1px solid #ccc;white-space:pre-line;">${items}</td>
                    <td style="padding:6px;border:1px solid #ccc;">${tr.dataset.service}</td>
                    <td style="padding:6px;border:1px solid #ccc;">${tr.dataset.serviceFee}</td>
                    <td style="padding:6px;border:1px solid #ccc;">${tr.dataset.discount}</td>
                    <td style="padding:6px;border:1px solid #ccc;text-align:right;">${tr.dataset.amount}</td>
                </tr>
            `;
        });

        const printWindow = window.open('', '', 'height=700,width=1000');
        printWindow.document.write(`
            <html><head><title>Transaction History</title>
            <style>
                body { font-family: Arial, sans-serif; padding:20px; }
                table { width: 100%; border-collapse: collapse; }
                th,td { border:1px solid #ccc; padding:8px; text-align:left; }
                th { background:#f4f4f4; }
            </style></head><body>
            <h2>Transaction History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Service</th>
                        <th>Service Fee</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>${tableRows}</tbody>
            </table>
            </body></html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endsection