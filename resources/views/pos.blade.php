@extends('layouts.default-layout')

@section('content')
<div class="flex flex-col h-full md:flex-row">
    <!-- Left Section (Items) -->
    <div class="flex-1 bg-white p-2 rounded shadow mr-2">
        <div class="flex mb-4 justify-between items-center">
            <h1 class="text-3xl font-bold">ITEMS</h1>
            <div class="flex">
                <input id="searchInput" type="text"
                    class="p-2 border border-gray-300 rounded-l w-full md:w-auto h-10"
                    placeholder="Search">
                <button onclick="filterItems()"
                    class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded-r w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="flex mb-4 flex-wrap gap-2" id="categoryButtons">
            <button class="category-btn bg-blue-600 text-white px-4 py-2 rounded" data-category="ALL">ALL</button>
            @foreach(['FOOD','MEDICINE','SUPPLEMENTS','GROOMING','ACCESSORIES'] as $cat)
                <button class="category-btn bg-gray-200 text-gray-600 px-4 py-2 rounded" data-category="{{ $cat }}">{{ $cat }}</button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 overflow-y-auto h-[calc(100vh-250px)]" id="itemGrid">
            @foreach($items as $item)
            <div class="item relative {{ $item->quantity == 0 ? 'pointer-events-none opacity-60' : '' }}"
                data-id="{{ $item->id }}" data-category="{{ $item->category }}"
                data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                data-stock="{{ $item->quantity }}">

                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                    class="w-full h-32 object-cover rounded-md mb-1 cursor-pointer item-image {{ $item->quantity == 0 ? 'grayscale' : '' }}">

                @if($item->quantity == 0)
                <div class="absolute top-1 left-1 bg-red-600 text-white text-[10px] font-bold px-1 rounded">OUT OF STOCK</div>
                @endif

                <h3 class="text-center font-semibold text-sm">{{ $item->name }}</h3>
                <p class="text-center text-xs text-gray-500">Stocks: {{ $item->quantity }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right Section (Cart) -->
    <div class="bg-white p-4 rounded shadow w-full md:w-1/3 mt-2 md:mt-0">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-4xl font-bold">Cart</h2>
        </div>

        <div class="overflow-x-auto mb-4">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="p-2 text-left">QTY</th>
                        <th class="p-2 text-left">PRODUCT NAME</th>
                        <th class="p-2 text-left">AMOUNT</th>
                        <th class="p-2 text-center">✕</th>
                    </tr>
                </thead>
                <tbody id="cartBody"></tbody>
            </table>
        </div>

        <div class="space-y-2 mb-4">
            <div class="flex justify-between items-center">
                <label for="customerName" class="font-semibold">Customer Name:</label>
                <input id="customerName" type="text" class="border p-1 w-1/2" placeholder="e.g., John Doe">
            </div>
            <div class="flex justify-between">
                <span>Service Fee:</span>
                <input id="serviceFee" type="number" step="0.01" value="0" class="w-24 border p-1 text-right">
            </div>
            <div class="flex justify-between">
                <span>Discount (%):</span>
                <input id="discount" type="number" step="0.01" value="0" class="w-24 border p-1 text-right">
            </div>
            <div class="flex justify-between font-bold">
                <span>Total:</span>
                <span id="totalAmount">₱0.00</span>
            </div>
        </div>

        <div class="flex justify-between">
            <button onclick="clearCart()" class="bg-gray-600 text-white px-4 py-2 rounded">Cancel</button>
            <button onclick="showConfirmationModal()" class="bg-blue-600 text-white px-4 py-2 rounded">Pay</button>
        </div>
    </div>
</div>

<!-- Hidden Submission Form -->
<form id="posForm" method="POST" action="{{ route('pos.store') }}" class="hidden">
    @csrf
    <input type="hidden" name="customer_name" id="formCustomerName" value="Customer">
    <input type="hidden" name="service_fee" id="formService">
    <input type="hidden" name="discount" id="formDiscount">
    <input type="hidden" name="total" id="formTotal">
    <div id="formItems"></div>
</form>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-md">
        <h2 class="text-2xl font-bold mb-4">Purchase Receipt</h2>
        <div id="receiptContent" class="text-sm mb-4"></div>
        <div class="flex justify-end gap-4">
            <button onclick="closeConfirmationModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
            <button onclick="submitCart()" class="bg-green-600 text-white px-4 py-2 rounded">Confirm</button>
        </div>
    </div>
</div>

<script>
    const categoryButtons = document.querySelectorAll('.category-btn');
    const items = document.querySelectorAll('.item');
    const searchInput = document.getElementById('searchInput');
    const cart = [];
    const cartBody = document.getElementById('cartBody');
    const totalEl = document.getElementById('totalAmount');

    categoryButtons.forEach(btn => btn.addEventListener('click', () => {
        categoryButtons.forEach(b => {
            b.classList.remove('bg-blue-600', 'text-white');
            b.classList.add('bg-gray-200', 'text-gray-600');
        });
        btn.classList.remove('bg-gray-200', 'text-gray-600');
        btn.classList.add('bg-blue-600', 'text-white');
        filterItems();
    }));

    function filterItems() {
        const sv = searchInput.value.toLowerCase();
        const cat = document.querySelector('.category-btn.bg-blue-600').dataset.category;
        items.forEach(it => {
            const name = it.dataset.name.toLowerCase();
            const matches = name.includes(sv) && (cat === 'ALL' || it.dataset.category === cat);
            it.style.display = matches ? '' : 'none';
        });
    }

    document.querySelectorAll('.item-image').forEach(img => {
        img.addEventListener('click', (e) => {
            const parent = e.target.closest('.item');
            const id = parent.dataset.id;
            let cartItem = cart.find(c => c.id == id);
            if (!cartItem) {
                cartItem = {
                    id,
                    name: parent.dataset.name,
                    price: parseFloat(parent.dataset.price),
                    qty: 0
                };
                cart.push(cartItem);
            }
            cartItem.qty++;
            renderCart();
        });
    });

    function renderCart() {
        cartBody.innerHTML = '';
        let total = 0;
        cart.forEach((c, i) => {
            const subtotal = c.qty * c.price;
            total += subtotal;
            cartBody.innerHTML += `
                <tr>
                    <td class="p-2">
                        <div class="flex items-center gap-1">
                            <button onclick="decreaseQty(${i})" class="px-2 bg-gray-300 rounded">–</button>
                            <span class="min-w-[24px] text-center">${c.qty}</span>
                            <button onclick="increaseQty(${i})" class="px-2 bg-gray-300 rounded">+</button>
                        </div>
                    </td>
                    <td class="p-2">${c.name}</td>
                    <td class="p-2">₱${subtotal.toFixed(2)}</td>
                    <td class="p-2 text-center cursor-pointer" onclick="removeItem(${i})">×</td>
                </tr>
            `;
        });
        calculateTotal(total);
    }

    function increaseQty(index) {
        cart[index].qty++;
        renderCart();
    }

    function decreaseQty(index) {
        if (cart[index].qty > 1) {
            cart[index].qty--;
        } else {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        cart.length = 0;
        renderCart();
    }

    function calculateTotal(subtotal) {
        const fee = parseFloat(document.getElementById('serviceFee').value) || 0;
        const discPercent = parseFloat(document.getElementById('discount').value) || 0;
        const discount = subtotal * (discPercent / 100);
        const total = subtotal + fee - discount;
        totalEl.textContent = `₱${total.toFixed(2)}`;
    }

    document.getElementById('serviceFee').addEventListener('input', () => renderCart());
    document.getElementById('discount').addEventListener('input', () => renderCart());

    function showConfirmationModal() {
        if (cart.length === 0) {
            alert("Cart is empty.");
            return;
        }

        let receipt = '<ul class="mb-2">';
        let subtotal = 0;
        cart.forEach(c => {
            const itemTotal = c.qty * c.price;
            subtotal += itemTotal;
            receipt += `<li>${c.qty}x ${c.name} - ₱${itemTotal.toFixed(2)}</li>`;
        });
        receipt += '</ul>';

        const fee = parseFloat(document.getElementById('serviceFee').value) || 0;
        const discPercent = parseFloat(document.getElementById('discount').value) || 0;
        const discount = subtotal * (discPercent / 100);
        const total = subtotal + fee - discount;

        receipt += `
            <p>Service Fee: ₱${fee.toFixed(2)}</p>
            <p>Discount (${discPercent}%): -₱${discount.toFixed(2)}</p>
            <p class="font-bold mt-2">TOTAL: ₱${total.toFixed(2)}</p>
        `;

        document.getElementById('receiptContent').innerHTML = receipt;
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeConfirmationModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function submitCart() {
        const form = document.getElementById('posForm');
        document.getElementById('formCustomerName').value = document.getElementById('customerName').value || 'Customer';
        document.getElementById('formService').value = document.getElementById('serviceFee').value;
        document.getElementById('formDiscount').value = document.getElementById('discount').value;
        document.getElementById('formTotal').value = parseFloat(totalEl.textContent.replace('₱', ''));

        const itemsDiv = document.getElementById('formItems');
        itemsDiv.innerHTML = '';
        cart.forEach((c, i) => {
            itemsDiv.innerHTML += `
                <input type="hidden" name="items[${i}][inventory_item_id]" value="${c.id}">
                <input type="hidden" name="items[${i}][quantity]" value="${c.qty}">
                <input type="hidden" name="items[${i}][amount]" value="${(c.qty * c.price).toFixed(2)}">
            `;
        });

        form.submit();
    }

    window.addEventListener('DOMContentLoaded', filterItems);
</script>
@endsection
