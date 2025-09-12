@extends('layouts.default-layout')

@section('content')
<div class="container mx-auto p-3">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold">INVENTORY</h1>
        <div class="flex justify-end space-x-2 items-center">

            <button onclick="toggleView()"
                class="bg-purple-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-purple-400">
                <i id="viewIcon" class="fas fa-th mr-2"></i>
                <span id="viewLabel">Grid View</span>
            </button>


            <button onclick="openModal('addModal')"
                class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-green-300">
                <i class="fas fa-plus mr-2"></i> Add
            </button>
        </div>
    </div>


    <div class="flex flex-wrap justify-between items-center mb-2">
        <div class="flex space-x-2" id="categoryFilter">
            <button class="category-btn px-4 py-2 rounded bg-gray-200" data-category="ALL">ALL</button>
            <button class="category-btn px-4 py-2 rounded" data-category="FOOD">FOOD</button>
            <button class="category-btn px-4 py-2 rounded" data-category="MEDICINE">MEDICINE</button>
            <button class="category-btn px-4 py-2 rounded" data-category="SUPPLEMENTS">SUPPLEMENTS</button>
            <button class="category-btn px-4 py-2 rounded" data-category="GROOMING">GROOMING</button>
            <button class="category-btn px-4 py-2 rounded" data-category="ACCESSORIES">ACCESSORIES</button>

            <button class="category-btn px-4 py-2 rounded bg-red-200" data-category="OUT_OF_STOCK">OUT OF STOCK</button>
            <button class="category-btn px-4 py-2 rounded bg-yellow-200" data-category="LOW_STOCK">LOW STOCK</button>
        </div>
        <div class="flex">
            <input class="p-2 border border-gray-300 rounded-l w-full md:w-auto h-10"
                id="searchInput" placeholder="Search" type="text" />
            <button
                class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded-r w-10 h-10 flex items-center justify-center"
                onclick="filterTable()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>


    <div id="inventoryContainer" class="grid grid-cols-2 md:grid-cols-4 gap-8 overflow-y-auto h-[calc(100vh-150px)]">
        @foreach ($items as $item)
        <div class="relative inventory-wrapper bg-grey rounded p-2 flex flex-col list-view-item"
            data-category="{{ $item->category }}"
            data-stock="{{ $item->quantity == 0 ? 'OUT_OF_STOCK' : ($item->quantity <= 5 ? 'LOW_STOCK' : 'IN_STOCK') }}"
            data-expiration_date="{{ $item->expiration_date }}">

            <img src="{{ asset($item->image) }}"
                alt="{{ $item->name }}"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-category="{{ $item->category }}"
                data-quantity="{{ $item->quantity }}"
                data-price="{{ $item->price }}"
                data-image="{{ asset($item->image) }}"
                data-expiration_date="{{ $item->expiration_date }}"
                class="inventory-item w-full h-48 object-cover rounded-md filter hover:brightness-[65%] cursor-pointer {{ $item->quantity == 0 ? 'grayscale' : '' }}" />


            <div class="grid-view-name mt-2 text-center font-semibold text-gray-800 cursor-pointer">
                {{ $item->name }}
            </div>


            <div class="list-view-info mt-2 hidden flex-1 cursor-pointer"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-category="{{ $item->category }}"
                data-quantity="{{ $item->quantity }}"
                data-price="{{ $item->price }}"
                data-image="{{ asset($item->image) }}"
                data-expiration_date="{{ $item->expiration_date }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset($item->image) }}" class="w-12 h-12 object-cover rounded">
                        <div>
                            <h3 class="font-bold">{{ $item->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $item->category }}</p>
                            <p class="text-sm">Qty: {{ $item->quantity }}</p>
                            <p class="text-sm">Exp: {{ $item->expiration_date ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="text-right font-semibold text-gray-800">
                        ₱{{ number_format($item->price, 2) }}
                    </div>
                </div>
            </div>

            @if ($item->quantity == 0)
            <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded">OUT OF STOCK</div>
            @elseif ($item->quantity <= 5)
                <div class="absolute top-2 left-2 bg-yellow-400 text-black text-xs font-semibold px-2 py-1 rounded">LOW STOCK
        </div>
        @endif
    </div>
    @endforeach
</div>
</div>


<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Add Inventory Item</h2>
            <button onclick="closeModal('addModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
        </div>
        <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block mb-1">Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Category</label>
                <select name="category" class="w-full border rounded px-3 py-2" required>
                    <option value="" disabled selected>Select a category</option>
                    <option value="FOOD">FOOD</option>
                    <option value="MEDICINE">MEDICINE</option>
                    <option value="SUPPLEMENTS">SUPPLEMENTS</option>
                    <option value="GROOMING">GROOMING</option>
                    <option value="ACCESSORIES">ACCESSORIES</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Quantity</label>
                <input type="number" name="quantity" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Price</label>
                <input type="number" step="0.01" name="price" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Expiration Date</label>
                <input type="month" name="expiration_date" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1">Image</label>
                <input type="file" name="image" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('addModal')" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Add</button>
            </div>
        </form>
    </div>
</div>


<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Edit Inventory Item</h2>
            <button onclick="closeModal('editModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="editId">
            <div class="mb-3">
                <label class="block mb-1">Name</label>
                <input type="text" name="name" id="editName" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Category</label>
                <select name="category" id="editCategory" class="w-full border rounded px-3 py-2" required>
                    <option value="" disabled>Select a category</option>
                    <option value="FOOD">FOOD</option>
                    <option value="MEDICINE">MEDICINE</option>
                    <option value="SUPPLEMENTS">SUPPLEMENTS</option>
                    <option value="GROOMING">GROOMING</option>
                    <option value="ACCESSORIES">ACCESSORIES</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Quantity</label>
                <input type="number" name="quantity" id="editQuantity" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Price</label>
                <input type="number" step="0.01" name="price" id="editPrice" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Expiration Date</label>
                <input type="month" name="expiration_date" id="editExpiration" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1">Current Image</label>
                <img id="editImagePreview" src="" alt="Current Image" class="w-full h-48 object-cover rounded mb-2">
                <button type="button" onclick="toggleImageInput()" class="bg-blue-500 text-white px-4 py-2 rounded">Change Photo</button>
                <input type="file" name="image" id="editImageInput" class="w-full border rounded px-3 py-2 mt-2 hidden">
            </div>
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                <button type="button" onclick="submitDelete()" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
            </div>
        </form>
    </div>
</div>


<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold" id="viewName">Item Details</h2>
            <button onclick="closeModal('viewModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
        </div>
        <div class="mb-4">
            <img id="viewImage" src="" alt="Item Image" class="w-full h-48 object-cover rounded">
        </div>
        <div class="mb-2"><strong>Category:</strong> <span id="viewCategory"></span></div>
        <div class="mb-2"><strong>Quantity:</strong> <span id="viewQuantity"></span></div>
        <div class="mb-2"><strong>Price:</strong> ₱<span id="viewPrice"></span></div>
        <div class="mb-2"><strong>Expiration:</strong> <span id="viewExpiration"></span></div>
        <div class="flex justify-end mt-4">
            <button id="editFromViewBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-400">
                <i class="fas fa-edit mr-2"></i> Edit
            </button>
        </div>
    </div>
</div>


<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center">
        <h2 class="text-xl font-bold mb-4 text-red-600">Delete Item</h2>
        <p class="mb-6">Are you sure you want to delete this item?</p>
        <div class="flex justify-center gap-4">
            <button onclick="closeModal('confirmDeleteModal')" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
            <button onclick="confirmDelete()" class="bg-red-500 text-white px-4 py-2 rounded">Yes, Delete</button>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>


<script>
    let isGridView = true;
    let currentDeleteId = null;
    let currentItem = null;

    function toggleView(save = true) {
        const container = document.getElementById('inventoryContainer');
        const viewLabel = document.getElementById('viewLabel');
        const viewIcon = document.getElementById('viewIcon');
        const listInfos = document.querySelectorAll('.list-view-info');
        const thumbnails = document.querySelectorAll('.inventory-item');
        const gridNames = document.querySelectorAll('.grid-view-name');

        if (isGridView) {
            container.classList.remove('grid', 'grid-cols-2', 'md:grid-cols-4', 'gap-8');
            container.classList.add('flex', 'flex-col', 'space-y-2');
            listInfos.forEach(info => info.classList.remove('hidden'));
            thumbnails.forEach(img => img.classList.add('hidden'));
            gridNames.forEach(name => name.classList.add('hidden'));
            viewLabel.textContent = "List View";
            viewIcon.classList.replace('fa-th', 'fa-list');
            if (save) localStorage.setItem('inventoryView', 'list');
        } else {
            container.classList.remove('flex', 'flex-col', 'space-y-2');
            container.classList.add('grid', 'grid-cols-2', 'md:grid-cols-4', 'gap-8');
            listInfos.forEach(info => info.classList.add('hidden'));
            thumbnails.forEach(img => img.classList.remove('hidden'));
            gridNames.forEach(name => name.classList.remove('hidden'));
            viewLabel.textContent = "Grid View";
            viewIcon.classList.replace('fa-list', 'fa-th');
            if (save) localStorage.setItem('inventoryView', 'grid');
        }
        isGridView = !isGridView;
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function openItem(el) {
        const {
            id,
            name,
            category,
            quantity,
            price,
            image,
            expiration_date
        } = el.dataset;
        currentItem = {
            id,
            name,
            category,
            quantity,
            price,
            image,
            expiration_date
        };
        document.getElementById('viewName').textContent = name;
        document.getElementById('viewCategory').textContent = category;
        document.getElementById('viewQuantity').textContent = quantity;
        document.getElementById('viewPrice').textContent = parseFloat(price).toFixed(2);
        document.getElementById('viewImage').src = image;
        document.getElementById('viewExpiration').textContent = expiration_date ? expiration_date : 'N/A';
        openModal('viewModal');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.inventory-item, .list-view-info').forEach(el => {
            el.addEventListener('click', () => openItem(el));
        });

        document.getElementById('editFromViewBtn').addEventListener('click', () => {
            if (!currentItem) return;
            document.getElementById('editId').value = currentItem.id;
            document.getElementById('editName').value = currentItem.name;
            document.getElementById('editCategory').value = currentItem.category;
            document.getElementById('editQuantity').value = currentItem.quantity;
            document.getElementById('editPrice').value = currentItem.price;
            document.getElementById('editExpiration').value = currentItem.expiration_date || '';
            document.getElementById('editImagePreview').src = currentItem.image;
            document.getElementById('editForm').action = `/inventory/${currentItem.id}`;
            currentDeleteId = currentItem.id;
            closeModal('viewModal');
            openModal('editModal');
        });

        const savedView = localStorage.getItem('inventoryView');
        if (savedView === 'list' && isGridView) toggleView(false);

        // Category/Stock Filter
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const category = btn.dataset.category;
                document.querySelectorAll('.inventory-wrapper').forEach(item => {
                    const itemCategory = item.dataset.category;
                    const itemStock = item.dataset.stock;
                    if (category === 'ALL' ||
                        (category === 'OUT_OF_STOCK' && itemStock === 'OUT_OF_STOCK') ||
                        (category === 'LOW_STOCK' && itemStock === 'LOW_STOCK') ||
                        itemCategory === category) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('bg-blue-500', 'text-white'));
                btn.classList.add('bg-blue-500', 'text-white');
            });
        });

        // 🔎 Live Search
        document.getElementById('searchInput').addEventListener('input', filterTable);
    });

    function filterTable() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.inventory-wrapper').forEach(item => {
            const itemName = item.querySelector('.grid-view-name').textContent.toLowerCase();
            if (itemName.includes(searchValue)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function toggleImageInput() {
        const input = document.getElementById('editImageInput');
        input.classList.toggle('hidden');
    }

    function submitDelete() {
        if (!currentDeleteId) return;
        currentDeleteId = currentItem.id;
        closeModal('editModal');
        openModal('confirmDeleteModal');
    }

    function confirmDelete() {
        if (!currentDeleteId) return;
        const form = document.getElementById('deleteForm');
        form.action = `/inventory/${currentDeleteId}`;
        form.submit();
    }
</script>
@endsection