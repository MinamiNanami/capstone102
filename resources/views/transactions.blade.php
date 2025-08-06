@extends('layouts.default-layout')

@section('content')
<div class="p-6">
    <h1 class="text-3xl font-bold mb-4">Transaction History</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($transactions->isEmpty())
        <p class="text-gray-500">No transactions found.</p>
    @else
        <div class="space-y-2">
            @foreach($transactions as $transaction)
                <div 
                    class="bg-white rounded shadow p-3 border flex justify-between items-center cursor-pointer hover:bg-gray-100"
                    onclick="showReceipt(this)"
                    data-name="{{ $transaction->customer_name }}"
                    data-date="{{ $transaction->created_at->format('F j, Y h:i A') }}"
                    data-items="@foreach($transaction->items as $item){{ $item->quantity }}x {{ $item->item_name }} - ₱{{ number_format($item->price, 2) }}@if(!$loop->last);@endif @endforeach"
                >
                    <div>
                        <p class="text-sm text-gray-600">{{ $transaction->created_at->format('F j, Y h:i A') }}</p>
                        <p class="text-md font-medium">{{ $transaction->customer_name }}</p>
                    </div>
                    <div class="text-md font-bold text-green-600">
                        ₱{{ number_format($transaction->items->sum('price'), 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Receipt Modal -->
<div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-[90%] max-w-md">
        <h2 class="text-2xl font-bold mb-4">Transaction Receipt</h2>
        <p><strong>Date:</strong> <span id="receiptDate"></span></p>
        <p><strong>Customer:</strong> <span id="receiptCustomer"></span></p>
        <div class="my-3 border-t pt-2" id="receiptItems"></div>
        <p class="text-right font-bold text-lg mt-2">Total: ₱<span id="receiptTotal">0.00</span></p>
        <div class="text-right mt-4">
            <button onclick="closeReceiptModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500">Close</button>
        </div>
    </div>
</div>

<script>
    function showReceipt(el) {
        const date = el.dataset.date;
        const customer = el.dataset.name;
        const itemsString = el.dataset.items || '';
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

        let computedTotal = 0;

        itemsArray.forEach(line => {
            const match = line.match(/^(\d+)x\s(.+?)\s-\s₱([\d,.]+)/);
            if (match) {
                const qty = parseInt(match[1]);
                const name = match[2];
                const itemTotal = parseFloat(match[3].replace(/,/g, ''));
                computedTotal += itemTotal;

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

        document.getElementById('receiptDate').textContent = date;
        document.getElementById('receiptCustomer').textContent = customer;
        document.getElementById('receiptItems').innerHTML = receiptTable;
        document.getElementById('receiptTotal').textContent = computedTotal.toFixed(2);

        document.getElementById('receiptModal').classList.remove('hidden');
        document.getElementById('receiptModal').classList.add('flex');
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.remove('flex');
        document.getElementById('receiptModal').classList.add('hidden');
    }
</script>
@endsection
