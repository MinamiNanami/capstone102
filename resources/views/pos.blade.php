@extends('layouts.default-layout')

@section('content')
<style>
/* List Mode */
.list-mode {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.list-mode .item {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    width: 100%;
    padding: 0.5rem;
    cursor: pointer;
    transition: background-color 0.2s;
}
.list-mode .item:hover {
    background-color: #f3f4f6; /* light gray hover */
}
.list-mode .item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.5rem;
}
.list-mode .item-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.list-mode .item h3,
.list-mode .item p {
    text-align: left;
    margin: 0;
}
</style>

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

        <!-- Grid/List Toggle Buttons -->
        <div class="flex justify-end mb-2 gap-2">
            <button id="gridViewBtn" class="bg-blue-600 text-white px-3 py-1 rounded">Grid View</button>
            <button id="listViewBtn" class="bg-gray-200 text-gray-600 px-3 py-1 rounded">List View</button>
        </div>

        <div class="flex mb-4 flex-wrap gap-2" id="categoryButtons">
            <button class="category-btn bg-blue-600 text-white px-4 py-2 rounded" data-category="ALL">ALL</button>
            @foreach(['FOOD','MEDICINE','SUPPLEMENTS','GROOMING','ACCESSORIES'] as $cat)
                <button class="category-btn bg-gray-200 text-gray-600 px-4 py-2 rounded" data-category="{{ $cat }}">{{ $cat }}</button>
            @endforeach
        </div>

        <div id="itemGrid" class="grid grid-cols-2 md:grid-cols-4 gap-3 overflow-y-auto h-[calc(100vh-250px)]">
            @foreach($items as $item)
            <div class="item relative {{ $item->quantity == 0 ? 'pointer-events-none opacity-60' : '' }}"
                data-id="{{ $item->id }}" data-category="{{ $item->category }}"
                data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                data-stock="{{ $item->quantity }}">
                
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                <div class="item-details">
                    <h3 class="font-semibold text-sm">{{ $item->name }}</h3>
                    <p class="text-xs text-gray-500">Category: {{ $item->category }}</p>
                    <p class="text-xs text-gray-500">Stocks: {{ $item->quantity }}</p>
                </div>

                @if($item->quantity == 0)
                <div class="absolute top-1 left-1 bg-red-600 text-white text-[10px] font-bold px-1 rounded">OUT OF STOCK</div>
                @endif
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
            <button onclick="showResetModal()" class="bg-gray-600 text-white px-4 py-2 rounded">Reset</button>
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

<!-- Purchase Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-md">
        <h2 class="text-2xl font-bold mb-4">Purchase Receipt</h2>
        <div id="receiptContent" class="text-sm mb-4"></div>
        <div class="flex justify-end gap-4">
            <button onclick="closeConfirmationModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
            <button onclick="submitCart()" class="bg-green-600 text-white px-4 py-2 rounded">Confirm</button>
            <button onclick="printReceipt()" class="bg-yellow-500 text-black px-4 py-2 rounded">Print</button>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-md">
        <h2 class="text-2xl font-bold mb-4">Reset Cart</h2>
        <p class="mb-4">Are you sure you want to reset the cart? All items and changes will be lost.</p>
        <div class="flex justify-end gap-4">
            <button onclick="closeResetModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
            <button onclick="confirmResetCart()" class="bg-red-600 text-white px-4 py-2 rounded">Reset</button>
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

const gridViewBtn = document.getElementById('gridViewBtn');
const listViewBtn = document.getElementById('listViewBtn');
const itemGrid = document.getElementById('itemGrid');

// Grid/List toggle with persistence
function setViewMode(mode){
    if(mode==='grid'){
        itemGrid.classList.remove('list-mode');
        gridViewBtn.classList.add('bg-blue-600','text-white');
        gridViewBtn.classList.remove('bg-gray-200','text-gray-600');
        listViewBtn.classList.add('bg-gray-200','text-gray-600');
        listViewBtn.classList.remove('bg-blue-600','text-white');
    } else {
        itemGrid.classList.add('list-mode');
        listViewBtn.classList.add('bg-blue-600','text-white');
        listViewBtn.classList.remove('bg-gray-200','text-gray-600');
        gridViewBtn.classList.add('bg-gray-200','text-gray-600');
        gridViewBtn.classList.remove('bg-blue-600','text-white');
    }
    localStorage.setItem('posViewMode', mode);
}
gridViewBtn.addEventListener('click',()=>setViewMode('grid'));
listViewBtn.addEventListener('click',()=>setViewMode('list'));

// Restore view mode
window.addEventListener('DOMContentLoaded', ()=>{
    const savedMode = localStorage.getItem('posViewMode') || 'grid';
    setViewMode(savedMode);
    filterItems();
});

// Category filter
categoryButtons.forEach(btn => btn.addEventListener('click', () => {
    categoryButtons.forEach(b=>{
        b.classList.remove('bg-blue-600','text-white');
        b.classList.add('bg-gray-200','text-gray-600');
    });
    btn.classList.remove('bg-gray-200','text-gray-600');
    btn.classList.add('bg-blue-600','text-white');
    filterItems();
}));

function filterItems(){
    const sv=searchInput.value.toLowerCase();
    const cat=document.querySelector('.category-btn.bg-blue-600').dataset.category;
    items.forEach(it=>{
        const name=it.dataset.name.toLowerCase();
        const matches=name.includes(sv) && (cat==='ALL'||it.dataset.category===cat);
        it.style.display=matches?'':'none';
    });
}

// Add to cart
function addToCart(parent){
    const id=parent.dataset.id;
    let cartItem=cart.find(c=>c.id==id);
    if(!cartItem){
        cartItem={id,name:parent.dataset.name,price:parseFloat(parent.dataset.price),qty:0,stock:parent.dataset.stock};
        cart.push(cartItem);
    }
    cartItem.qty++;
    renderCart();
}
document.querySelectorAll('.item').forEach(div=>{
    div.addEventListener('click', e=>{
        if(e.target.tagName.toLowerCase()==='input') return;
        addToCart(div);
    });
});

function renderCart(){
    cartBody.innerHTML='';
    let subtotal=0;
    cart.forEach((c,i)=>{
        const itemTotal=c.qty*c.price;
        subtotal+=itemTotal;
        cartBody.innerHTML+=`
            <tr>
                <td class="p-2"><input type="number" min="1" value="${c.qty}" class="border w-16 text-center" onchange="updateQty(${i}, this.value)"></td>
                <td class="p-2">${c.name}</td>
                <td class="p-2">₱${itemTotal.toFixed(2)}</td>
                <td class="p-2 text-center cursor-pointer" onclick="removeItem(${i})">×</td>
            </tr>
        `;
    });
    calculateTotal(subtotal);
}

function updateQty(index,value){
    let qty=parseInt(value);
    if(isNaN(qty)||qty<1) qty=1;
    cart[index].qty=qty;
    renderCart();
}

function removeItem(index){ cart.splice(index,1); renderCart(); }

function calculateTotal(subtotal){
    const fee=parseFloat(document.getElementById('serviceFee').value)||0;
    const discPercent=parseFloat(document.getElementById('discount').value)||0;
    const totalBeforeDiscount=subtotal+fee;
    const discount=totalBeforeDiscount*(discPercent/100);
    const total=totalBeforeDiscount-discount;
    totalEl.textContent=`₱${total.toFixed(2)}`;
}

document.getElementById('serviceFee').addEventListener('input',()=>renderCart());
document.getElementById('discount').addEventListener('input',()=>renderCart());

// Reset modal
function showResetModal(){ document.getElementById('resetModal').classList.remove('hidden'); }
function closeResetModal(){ document.getElementById('resetModal').classList.add('hidden'); }
function confirmResetCart(){
    cart.length=0;
    renderCart();
    document.getElementById('serviceFee').value=0;
    document.getElementById('discount').value=0;
    totalEl.textContent='₱0.00';
    closeResetModal();
}

// Purchase modal
function showConfirmationModal(){
    if(cart.length===0){ alert("Cart is empty."); return; }
    let receipt='<ul class="mb-2">';
    let subtotal=0;
    cart.forEach(c=>{
        const itemTotal=c.qty*c.price;
        subtotal+=itemTotal;
        receipt+=`<li>${c.qty}x ${c.name} - ₱${itemTotal.toFixed(2)}</li>`;
    });
    receipt+='</ul>';
    const fee=parseFloat(document.getElementById('serviceFee').value)||0;
    const discPercent=parseFloat(document.getElementById('discount').value)||0;
    const totalBeforeDiscount=subtotal+fee;
    const discount=totalBeforeDiscount*(discPercent/100);
    const total=totalBeforeDiscount-discount;
    receipt+=`<p>Service Fee: ₱${fee.toFixed(2)}</p>
              <p>Discount (${discPercent}%): -₱${discount.toFixed(2)}</p>
              <p class="font-bold mt-2">TOTAL: ₱${total.toFixed(2)}</p>`;
    document.getElementById('receiptContent').innerHTML=receipt;
    document.getElementById('confirmModal').classList.remove('hidden');
}
function closeConfirmationModal(){ document.getElementById('confirmModal').classList.add('hidden'); }

function submitCart(){
    const form=document.getElementById('posForm');
    document.getElementById('formCustomerName').value=document.getElementById('customerName').value||'Customer';
    document.getElementById('formService').value=document.getElementById('serviceFee').value;
    document.getElementById('formDiscount').value=document.getElementById('discount').value;
    document.getElementById('formTotal').value=parseFloat(totalEl.textContent.replace('₱',''));
    const itemsDiv=document.getElementById('formItems');
    itemsDiv.innerHTML='';
    cart.forEach((c,i)=>{
        itemsDiv.innerHTML+=`
            <input type="hidden" name="items[${i}][inventory_item_id]" value="${c.id}">
            <input type="hidden" name="items[${i}][quantity]" value="${c.qty}">
            <input type="hidden" name="items[${i}][amount]" value="${(c.qty*c.price).toFixed(2)}">
        `;
    });
    form.submit();
}

// Enhanced print receipt function
function printReceipt() {
    const customerName = document.getElementById('customerName').value || 'Customer';
    const serviceFee = parseFloat(document.getElementById('serviceFee').value) || 0;
    const discountPercent = parseFloat(document.getElementById('discount').value) || 0;
    
    // Calculate totals
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.qty * item.price;
    });
    
    const totalBeforeDiscount = subtotal + serviceFee;
    const discountAmount = totalBeforeDiscount * (discountPercent / 100);
    const finalTotal = totalBeforeDiscount - discountAmount;
    
    // Generate receipt number (you can customize this logic)
    const receiptNumber = '#' + Date.now().toString().slice(-6);
    const currentDate = new Date().toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    
    // Create print window
    const printWindow = window.open('', '', 'height=700,width=500');
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Receipt</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    max-width: 400px;
                    margin: 0 auto;
                    padding: 20px;
                    line-height: 1.4;
                    color: #333;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                
                .logo {
                    width: 60px;
                    height: 60px;
                    background-color: #f0f0f0;
                    border-radius: 50%;
                    margin: 0 auto 15px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 24px;
                    color: #666;
                }
                
                .clinic-name {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                
                .clinic-address {
                    font-size: 12px;
                    color: #666;
                    margin-bottom: 2px;
                }
                
                .receipt-number {
                    font-size: 24px;
                    font-weight: bold;
                    text-align: right;
                    margin: 20px 0;
                }
                
                .customer-info {
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #eee;
                }
                
                .customer-name {
                    font-size: 14px;
                    margin-bottom: 10px;
                }
                
                .items-summary {
                    font-size: 14px;
                    color: #666;
                    margin-bottom: 20px;
                }
                
                .items-list {
                    margin-bottom: 30px;
                }
                
                .item-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 8px 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                
                .item-qty {
                    font-size: 14px;
                    color: #666;
                    width: 30px;
                }
                
                .item-name {
                    flex: 1;
                    font-size: 14px;
                    margin-left: 10px;
                }
                
                .item-price {
                    font-size: 14px;
                    font-weight: bold;
                }
                
                .totals-section {
                    border-top: 2px solid #333;
                    padding-top: 15px;
                    margin-bottom: 30px;
                }
                
                .total-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 8px;
                    font-size: 14px;
                }
                
                .final-total {
                    font-size: 18px;
                    font-weight: bold;
                    border-top: 1px solid #333;
                    padding-top: 10px;
                    margin-top: 10px;
                }
                
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #eee;
                }
                
                .footer-message {
                    font-style: italic;
                    margin-bottom: 10px;
                    color: #666;
                }
                
                .date-time {
                    font-size: 12px;
                    color: #888;
                }
                
                @media print {
                    body { margin: 0; }
                }
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
                <div class="customer-name">👤 ${customerName}</div>
            </div>
            
            <div class="items-summary">${cart.length} items (Qty.: ${cart.reduce((total, item) => total + item.qty, 0)})</div>
            
            <div class="items-list">
                ${cart.map(item => `
                    <div class="item-row">
                        <div class="item-qty">${item.qty}×</div>
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">₱${(item.qty * item.price).toFixed(2)}</div>
                    </div>
                `).join('')}
            </div>
            
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₱${subtotal.toFixed(2)}</span>
                </div>
                ${serviceFee > 0 ? `
                    <div class="total-row">
                        <span>Service Fee:</span>
                        <span>₱${serviceFee.toFixed(2)}</span>
                    </div>
                ` : ''}
                ${discountPercent > 0 ? `
                    <div class="total-row">
                        <span>Discount (${discountPercent}%):</span>
                        <span>-₱${discountAmount.toFixed(2)}</span>
                    </div>
                ` : ''}
                <div class="total-row final-total">
                    <span>Total:</span>
                    <span>₱${finalTotal.toFixed(2)}</span>
                </div>
            </div>
            
            <div class="footer">
                <div class="footer-message">Let your pets be our concern too<br>Thanks & God Bless!</div>
                <div class="date-time">${currentDate}</div>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for content to load then print
    printWindow.onload = function() {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    };
}

window.addEventListener('DOMContentLoaded',filterItems);
</script>
@endsection