@extends('layouts.default-layout')

@section('content')
<div class="w-full p-4">
    <h2 class="text-xl font-bold text-black mb-4">CLIENTS RECORDS</h2>

    @if(session('success'))
    <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    {{-- Search + Filter --}}
    <form method="GET" action="{{ route('registered') }}" class="mb-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex gap-2 w-full md:w-auto">
            <input type="text" id="liveSearch" name="search" value="{{ request('search') }}" placeholder="Search by owner or pet..." class="border p-2 rounded w-full md:w-64">

            <select name="sort" class="border p-2 rounded">
                <option value="">Sort By</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                <option value="date_latest" {{ request('sort') == 'date_latest' ? 'selected' : '' }}>Latest Date</option>
                <option value="date_oldest" {{ request('sort') == 'date_oldest' ? 'selected' : '' }}>Oldest Date</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded">
                Search
            </button>
            <button type="button" id="reset-filters" class="bg-gray-500 hover:bg-gray-400 text-white px-4 py-2 rounded">
                Reset
            </button>
        </div>
    </form>

    {{-- Scrollable Table Container --}}
    <div class="overflow-auto border border-gray-300 rounded max-h-[80vh]">
        <table id="patientsTable" class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-blue-400 text-white sticky top-0 z-10">
                <tr>
                    <th class="border p-2">DATE</th>
                    <th class="border p-2">OWNER</th>
                    <th class="border p-2">PET NAME</th>
                    <th class="border p-2">PET TYPE</th>
                    <th class="border p-2">GENDER</th>
                    <th class="border p-2">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                <tr class="hover:bg-gray-100">
                    <td class="border p-2">{{ $patient->updated_at->format('Y-m-d') }}</td>
                    <td class="border p-2">{{ $patient->owner_name }}</td>
                    <td class="border p-2">{{ $patient->pet_name }}</td>
                    <td class="border p-2">{{ $patient->pet_type }}</td>
                    <td class="border p-2">{{ $patient->gender }}</td>
                    <td class="border p-2 space-x-1">
                        <form method="GET" action="{{ route('registered') }}" class="inline">
                            <input type="hidden" name="show" value="{{ $patient->id }}">
                            <button class="bg-yellow-500 hover:bg-yellow-400 text-black px-2 py-1 rounded text-sm">View</button>
                        </form>
                        @if($patient->history)
                        <button onclick="document.getElementById('modal-hist-{{ $patient->id }}').classList.remove('hidden')"
                            class="bg-blue-500 hover:bg-blue-400 text-white px-2 py-1 rounded text-sm">
                            History
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- History Modals --}}
@foreach($patients as $patient)
@if($patient->history)
<div id="modal-hist-{{ $patient->id }}"
    class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative overflow-auto max-h-[80vh] shadow-lg">

        <button onclick="document.getElementById('modal-hist-{{ $patient->id }}').classList.add('hidden')"
            class="absolute top-2 right-2 text-gray-600 hover:text-black text-2xl">&times;</button>


        <div class="flex items-center mb-4">
            <i class="fas fa-notes-medical text-teal-500 text-2xl mr-2"></i>
            <h2 class="text-xl font-bold text-gray-800">
                Medical History for <span class="text-blue-600">{{ $patient->pet_name }}</span>
            </h2>
        </div>


        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            @foreach(explode("\n\n", $patient->history) as $entry)
            @if(trim($entry) !== '')
            <div class="flex items-start gap-3 border-b pb-2">
                <div class="flex-shrink-0">
                    <i class="fas fa-history text-gray-500 mt-1"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $entry }}</p>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif
@endforeach

{{-- Check-up View Modal --}}
@if(request('show'))
@php
$selected = $patients->where('id', request('show'))->first();
$checkups = $selected->checkups ?? collect();
@endphp

<div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-auto relative">
        <a href="{{ route('registered') }}" class="absolute top-2 right-2 text-gray-600 hover:text-black text-xl">&times;</a>
        <h2 class="text-2xl font-bold mb-4 text-center">Client: {{ $selected->owner_name }} | Pet: {{ $selected->pet_name }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm">
            <div><strong>Contact:</strong> {{ $selected->contact_number }}</div>
            <div><strong>Email:</strong> {{ $selected->email }}</div>
            <div><strong>Address:</strong> {{ $selected->address }}</div>
            <div><strong>Pet Type:</strong> {{ $selected->pet_type }}</div>
            <div><strong>Breed:</strong> {{ $selected->breed }}</div>
            <div><strong>Gender:</strong> {{ $selected->gender }}</div>
            <div><strong>Birthday:</strong> {{ $selected->birthday }}</div>
            <div><strong>Markings:</strong> {{ $selected->markings }}</div>
        </div>

        <hr class="my-4">

        <h3 class="text-lg font-semibold mb-2">Check-up History</h3>
        <div class="overflow-auto max-h-64 border border-gray-300 rounded mb-4">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="border p-2">Date</th>
                        <th class="border p-2">Disease</th>
                        <th class="border p-2">Diagnosis</th>
                        <th class="border p-2">Vital Signs</th>
                        <th class="border p-2">Treatment</th>
                        <th class="border p-2">Diagnosed By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($checkups as $checkup)
                    <tr>
                        <td class="border p-2">{{ $checkup->created_at->format('Y-m-d H:i') }}</td>
                        <td class="border p-2">{{ $checkup->disease }}</td>
                        <td class="border p-2">{{ $checkup->diagnosis }}</td>
                        <td class="border p-2">{{ $checkup->vital_signs }}</td>
                        <td class="border p-2">{{ $checkup->treatment }}</td>
                        <td class="border p-2">{{ $checkup->diagnosed_by }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="border p-2 text-center text-gray-500">No check-up records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-semibold mb-2">Add New Check-up</h3>
        <form method="POST" action="{{ route('registered.checkup.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @csrf
            <input type="hidden" name="pet_inventory_id" value="{{ $selected->id }}">

            <div>
                <label>Date</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Disease</label>
                <input type="text" name="disease" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label>Diagnosis</label>
                <input type="text" name="diagnosis" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Vital Signs</label>
                <input type="text" name="vital_signs" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Treatment</label>
                <input type="text" name="treatment" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Diagnosed By</label>
                <select name="diagnosed_by" class="w-full border p-2 rounded" required>
                    <option value="" disabled selected>Select Veterinarian</option>
                    <option value="Dr. 1">Dr. 1</option>
                    <option value="Dr. 2">Dr. 2</option>
                </select>
            </div>

            {{-- 🗓️ Appointment fields --}}
            <div>
                <label>Next Appointment Date</label>
                <input type="date" name="next_appointment" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Next Appointment Time</label>
                <input type="time" name="next_time" class="w-full border p-2 rounded">
            </div>

            <div class="md:col-span-2 text-right mt-2">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-400">
                    Save Check-Up
                </button>
            </div>
        </form>

        <hr class="my-4">

        <h3 class="text-lg font-semibold mb-2">Add Medical History Note</h3>
        <form method="POST" action="{{ route('registered.history.store') }}" class="text-sm">
            @csrf
            <input type="hidden" name="pet_inventory_id" value="{{ $selected->id }}">

            <div class="mb-3">
                <label class="block mb-1">History Note</label>
                <textarea name="history" rows="3" class="w-full border p-2 rounded" placeholder="Enter additional medical history..."></textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-400">
                    Save History Note
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Close modals with Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                document.querySelectorAll('.modal').forEach(modal => modal.classList.add('hidden'));
            }
        });

        // Close modal by clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) modal.classList.add('hidden');
            });
        });

        // Reset search and sort safely
        const resetBtn = document.getElementById('reset-filters');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('registered') }}";
            });
        }

        // ✅ Live Search
        const searchInput = document.getElementById('liveSearch');
        const table = document.getElementById('patientsTable');
        const tableRows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase().trim();

            tableRows.forEach(row => {
                const rowText = Array.from(row.querySelectorAll('td'))
                    .map(td => td.textContent.toLowerCase())
                    .join(' ');

                row.style.display = rowText.includes(value) ? '' : 'none';
            });
        });
    });
</script>
@endsection