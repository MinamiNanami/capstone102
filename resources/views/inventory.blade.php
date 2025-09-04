@extends('layouts.default-layout')

@section('content')
<div class="container mx-auto p-3">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold">INVENTORY</h1>
        <div class="flex justify-end space-x-2 items-center">
            <!-- View Toggle -->
            <button onclick="toggleView()"
                class="bg-purple-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-purple-400">
                <i id="viewIcon" class="fas fa-th mr-2"></i>
                <span id="viewLabel">Grid View</span>
            </button>

            <!-- Add / Edit Buttons -->
            <button onclick="openModal('addModal')"
                class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-green-300">
                <i class="fas fa-plus mr-2"></i> Add
            </button>
            <button onclick="toggleEditMode()"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-blue-300">
                <i class="fas fa-edit mr-2"></i> Edit
            </button>
            <span id="editModeLabel" class="text-sm font-semibold text-gray-600">Edit Mode: OFF</span>
        </div>
    </div>

    <!-- Category & Search -->
    <div class="flex flex-wrap justify-between items-center mb-2">
        <div class="flex space-x-2" id="categoryFilter">
            <button class="category-btn px-4 py-2 rounded" data-category="ALL">ALL</button>
            <button class="category-btn px-4 py-2 rounded" data-category="FOOD">FOOD</button>
            <button class="category-btn px-4 py-2 rounded" data-category="MEDICINE">MEDICINE</button>
            <button class="category-btn px-4 py-2 rounded" data-category="SUPPLEMENTS">SUPPLEMENTS</button>
            <button class="category-btn px-4 py-2 rounded" data-category="GROOMING">GROOMING</button>
            <button class="category-btn px-4 py-2 rounded" data-category="ACCESSORIES">ACCESSORIES</button>
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

    <!-- Inventory Container -->
    <div id="inventoryContainer" class="grid grid-cols-2 md:grid-cols-4 gap-8 overflow-y-auto h-[calc(100vh-150px)]">
        @foreach ($items as $item)
        <div class="relative inventory-wrapper bg-grey border-2 rounded p-2 flex flex-col list-view-item">
            <!-- Thumbnail for Grid -->
            <img src="{{ asset('storage/' . $item->image) }}"
                alt="{{ $item->name }}"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-category="{{ $item->category }}"
                data-quantity="{{ $item->quantity }}"
                data-price="{{ $item->price }}"
                data-image="{{ asset('storage/' . $item->image) }}"
                class="inventory-item w-full h-48 object-cover rounded-md filter hover:brightness-[65%] cursor-pointer {{ $item->quantity == 0 ? 'grayscale' : '' }}" />

            <!-- List View Info -->
            <div class="list-view-info mt-2 hidden flex-1 cursor-pointer"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-category="{{ $item->category }}"
                data-quantity="{{ $item->quantity }}"
                data-price="{{ $item->price }}"
                data-image="{{ asset('storage/' . $item->image) }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('storage/' . $item->image) }}" class="w-12 h-12 object-cover rounded">
                        <div>
                            <h3 class="font-bold">{{ $item->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $item->category }}</p>
                            <p class="text-sm">Qty: {{ $item->quantity }}</p>
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
<!-- ADD MODAL -->
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

<!-- EDIT MODAL -->
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

<!-- VIEW MODAL -->
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
    </div>
</div>

<!-- CONFIRM DELETE MODAL -->
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

<!-- ========== SCRIPTS ========== -->
<script>
    let isGridView = true;
    let editMode = false;

    function toggleView(save = true) {
        const container = document.getElementById('inventoryContainer');
        const viewLabel = document.getElementById('viewLabel');
        const viewIcon = document.getElementById('viewIcon');
        const listInfos = document.querySelectorAll('.list-view-info');
        const thumbnails = document.querySelectorAll('.inventory-item');

        if (isGridView) {
            // Switch to list view
            container.classList.remove('grid', 'grid-cols-2', 'md:grid-cols-4', 'gap-8');
            container.classList.add('flex', 'flex-col', 'space-y-2');

            listInfos.forEach(info => info.classList.remove('hidden'));
            thumbnails.forEach(img => img.classList.add('hidden'));

            viewLabel.textContent = "List View";
            viewIcon.classList.remove('fa-th');
            viewIcon.classList.add('fa-list');

            if (save) localStorage.setItem('inventoryView', 'list');
        } else {
            // Switch to grid view
            container.classList.remove('flex', 'flex-col', 'space-y-2');
            container.classList.add('grid', 'grid-cols-2', 'md:grid-cols-4', 'gap-8');

            listInfos.forEach(info => info.classList.add('hidden'));
            thumbnails.forEach(img => img.classList.remove('hidden'));

            viewLabel.textContent = "Grid View";
            viewIcon.classList.remove('fa-list');
            viewIcon.classList.add('fa-th');

            if (save) localStorage.setItem('inventoryView', 'grid');
        }

        isGridView = !isGridView;
    }

    function toggleEditMode() {
        editMode = !editMode;
        document.getElementById('editModeLabel').textContent = "Edit Mode: " + (editMode ? "ON" : "OFF");
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden', 'opacity-0');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function openItem(el) {
        const id = el.dataset.id;
        const name = el.dataset.name;
        const category = el.dataset.category;
        const quantity = el.dataset.quantity;
        const price = el.dataset.price;
        const image = el.dataset.image;

        if (editMode) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editCategory').value = category;
            document.getElementById('editQuantity').value = quantity;
            document.getElementById('editPrice').value = price;
            document.getElementById('editImagePreview').src = image;
            document.getElementById('editForm').action = `/inventory/${id}`;
            openModal('editModal');
        } else {
            document.getElementById('viewName').textContent = name;
            document.getElementById('viewCategory').textContent = category;
            document.getElementById('viewQuantity').textContent = quantity;
            document.getElementById('viewPrice').textContent = parseFloat(price).toFixed(2);
            document.getElementById('viewImage').src = image;
            openModal('viewModal');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.inventory-item').forEach(img => {
            img.addEventListener('click', () => openItem(img));
        });
        document.querySelectorAll('.list-view-info').forEach(row => {
            row.addEventListener('click', () => openItem(row));
        });

        // Restore last view from localStorage
        const savedView = localStorage.getItem('inventoryView');
        if (savedView === 'list' && isGridView) toggleView(false);
    });
</script>
@endsection