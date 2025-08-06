@extends('layouts.default-layout')

@section('content')
<div class="container mx-auto p-3">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold">INVENTORY</h1>
        <div class="flex justify-end space-x-2 items-center">
            <button onclick="openModal('addModal')"
                class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-green-300">
                <i class="fas fa-plus mr-2"></i>
                Add
            </button>
            <button onclick="toggleEditMode()"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-blue-300">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </button>
            <span id="editModeLabel" class="text-sm font-semibold text-gray-600">Edit Mode: OFF</span>
        </div>
    </div>

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

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 overflow-y-auto h-[calc(100vh-150px)]" id="inventoryGrid">
        @foreach ($items as $item)
        <div class="relative inventory-wrapper">
            <img
                src="{{ asset('storage/' . $item->image) }}"
                alt="{{ $item->name }}"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-category="{{ $item->category }}"
                data-quantity="{{ $item->quantity }}"
                data-price="{{ $item->price }}"
                data-image="{{ asset('storage/' . $item->image) }}"
                class="inventory-item w-full h-48 object-cover rounded-md filter hover:brightness-[65%] cursor-pointer {{ $item->quantity == 0 ? 'grayscale' : '' }}" />

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

<!-- SCRIPT -->
<script>
    const categoryButtons = document.querySelectorAll('.category-btn');
    const inventoryItems = document.querySelectorAll('.inventory-item');
    const searchInput = document.getElementById('searchInput');
    const editModeLabel = document.getElementById('editModeLabel');
    let selectedItemId = null;
    let isEditMode = false;

    function resetCategoryButtonStyles() {
        categoryButtons.forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-black');
        });
    }

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            resetCategoryButtonStyles();
            btn.classList.remove('bg-gray-200', 'text-black');
            btn.classList.add('bg-blue-600', 'text-white');
            const category = btn.dataset.category;
            filterItems(category, searchInput.value);
        });
    });

    function filterItems(category, keyword) {
        document.querySelectorAll('.inventory-wrapper').forEach(wrapper => {
            const img = wrapper.querySelector('.inventory-item');
            const itemCategory = img.dataset.category.toUpperCase();
            const itemAlt = img.alt.toUpperCase();
            const matchCategory = (category === 'ALL' || itemCategory === category);
            const matchSearch = itemAlt.includes(keyword.toUpperCase());
            wrapper.style.display = (matchCategory && matchSearch) ? '' : 'none';
        });
    }

    function filterTable() {
        const activeBtn = document.querySelector('.category-btn.bg-blue-600');
        const activeCategory = activeBtn ? activeBtn.dataset.category : 'ALL';
        filterItems(activeCategory, searchInput.value);
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('flex');
        document.getElementById(id).classList.add('hidden');
    }

    function toggleEditMode() {
        isEditMode = !isEditMode;
        editModeLabel.textContent = isEditMode ? 'Edit Mode: ON' : 'Edit Mode: OFF';
        editModeLabel.classList.toggle('text-red-600', isEditMode);
        editModeLabel.classList.toggle('text-gray-600', !isEditMode);
    }

    function toggleImageInput() {
        document.getElementById('editImageInput').classList.toggle('hidden');
    }

    inventoryItems.forEach(img => {
        img.addEventListener('click', () => {
            selectedItemId = img.dataset.id;

            if (isEditMode) {
                document.getElementById('editId').value = selectedItemId;
                document.getElementById('editName').value = img.dataset.name;
                document.getElementById('editCategory').value = img.dataset.category;
                document.getElementById('editQuantity').value = img.dataset.quantity;
                document.getElementById('editPrice').value = img.dataset.price;
                document.getElementById('editImagePreview').src = img.dataset.image;
                document.getElementById('editForm').action = `/inventory/${selectedItemId}`;
                openModal('editModal');
            } else {
                document.getElementById('viewImage').src = img.src;
                document.getElementById('viewName').textContent = img.dataset.name;
                document.getElementById('viewCategory').textContent = img.dataset.category;
                document.getElementById('viewQuantity').textContent = img.dataset.quantity;
                document.getElementById('viewPrice').textContent = parseFloat(img.dataset.price).toFixed(2);
                openModal('viewModal');
            }
        });
    });

    function submitDelete() {
        openModal('confirmDeleteModal');
    }

    function confirmDelete() {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/inventory/${selectedItemId}`;
        deleteForm.submit();
    }

    window.addEventListener('DOMContentLoaded', () => {
        categoryButtons[0].click(); // Trigger "ALL" filter on page load
    });
</script>
@endsection