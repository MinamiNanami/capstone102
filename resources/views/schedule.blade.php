@extends('layouts.default-layout')
@section('content')

<style>
    html,
    body {
        overflow: hidden;
        height: 100%;
    }
</style>

<!-- Sticky Header -->
<div class="sticky top-0 bg-blue-500 z-10 h-12 flex items-center" id="stickyHeader">
    <div class="flex justify-between items-center h-12 w-full px-4">
        <!-- Left: menu icon -->
        <div class="flex items-center h-12">
            <span class="bg-blue-500 p-2 rounded-md text-3xl cursor-pointer md:hidden h-12 flex items-center"></span>
        </div>
        <!-- Right: date input, search, add -->
        <div class="flex items-center space-x-2">
            <input class="border border-gray-300 rounded px-2 py-1" id="date-picker" type="date" />
            <button class="text-white text-xl" id="search-btn" title="Search by Date">
                <i class="fas fa-search"></i>
            </button>
            <button onclick="openModal('addEventModal')" class="text-white text-xl" title="Add Event">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
</div>

<div class="w-full h-full">
    <div class="max-w-full h-full mx-auto p-4 flex flex-col">
        <div class="flex flex-wrap items-center mb-4 space-x-2">
            <button class="bg-white border border-gray-300 rounded-full px-4 py-2 text-gray-700" id="today">Today</button>
            <button class="text-gray-500" id="prev-year" title="Previous Year"><i class="fas fa-angle-double-left"></i></button>
            <button class="text-gray-500" id="prev-month" title="Previous Month"><i class="fas fa-chevron-left"></i></button>
            <h2 class="text-xl font-semibold text-gray-900" id="month-year">Month Year</h2>
            <button class="text-gray-500" id="next-month" title="Next Month"><i class="fas fa-chevron-right"></i></button>
            <button class="text-gray-500" id="next-year" title="Next Year"><i class="fas fa-angle-double-right"></i></button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden flex-grow flex flex-col">
            <div class="grid grid-cols-7 text-center text-gray-500 font-medium border-b border-gray-200">
                <div class="py-2">SUN</div>
                <div class="py-2">MON</div>
                <div class="py-2">TUE</div>
                <div class="py-2">WED</div>
                <div class="py-2">THU</div>
                <div class="py-2">FRI</div>
                <div class="py-2">SAT</div>
            </div>
            <div class="grid grid-cols-7 text-center border-b border-gray-200 flex-grow text-sm md:text-base" id="calendar-days"></div>
        </div>
    </div>

    <!-- Hidden schedule data -->
    <div id="schedule-data" class="hidden">
        @foreach ($schedules as $s)
        <div class="event"
            data-title="{{ $s->title }}"
            data-date="{{ \Carbon\Carbon::parse($s->date)->format('Y-m-d') }}"
            data-description="{{ $s->description }}"
            data-time="{{ $s->time }}">
        </div>
        @endforeach
    </div>

    <!-- Add Schedule Modal -->
    <div id="addEventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Add Schedule</h2>
                <button onclick="closeModal('addEventModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
            </div>
            <form action="{{ route('schedule.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block mb-1">Title</label>
                    <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1">Customer Name</label>
                    <input type="text" name="customer_name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1">Phone Number</label>
                    <input type="text" name="phone_number" class="w-full border rounded px-3 py-2" required placeholder="e.g. 639171071234">
                </div>
                <div class="mb-3">
                    <label class="block mb-1">Date</label>
                    <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1">Time</label>
                    <input type="time" name="time" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Description</label>
                    <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('addEventModal')" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Day View Modal -->
    <div id="dayEventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-lg p-6 w-[95%] max-w-[1400px] max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold" id="dayEventDate"></h2>
                <button onclick="closeModal('dayEventModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
            </div>
            <div class="relative border-l border-gray-300 h-[1040px]" id="timeline">
                @for ($halfHour = 6 * 2; $halfHour <= 18 * 2; $halfHour++)
                    @php
                    $hour=floor($halfHour / 2);
                    $minute=($halfHour % 2) * 30;
                    $label=($hour % 12===0 ? 12 : $hour % 12) . ':' . str_pad($minute, 2, '0' , STR_PAD_LEFT) . ' ' . ($hour>= 12 ? 'PM' : 'AM');
                    $top = (($hour - 6) * 80) + ($minute * (80 / 60));
                    @endphp
                    <div class="absolute left-0 flex items-center w-full text-xs text-gray-500" style="top: {{ $top }}px; height: 40px;">
                        <div class="w-24 text-right pr-2 font-semibold">{{ $label }}</div>
                        <div class="border-t border-dashed border-gray-300 flex-grow ml-2"></div>
                    </div>
                    @endfor
                    <div id="dayEventList" class="absolute left-[7.5rem] right-4 top-0"></div>
            </div>
        </div>
    </div>

    <!-- View Description Modal (NEW) -->
    <div id="viewDescriptionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold" id="viewTitle"></h2>
                <button onclick="closeModal('viewDescriptionModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
            </div>
            <p id="viewDescription" class="text-gray-700"></p>
        </div>
    </div>

    <!-- Success / Error Messages -->
    @if(session('success'))
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-green-500 text-white rounded-lg p-6 w-full max-w-md text-center">
            {{ session('success') }}
        </div>
    </div>
    <script>
        setTimeout(() => document.getElementById('successModal')?.remove(), 3000);
    </script>
    @endif

    @if ($errors->any())
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-red-500 text-white rounded-lg p-6 w-full max-w-md text-center">
            {{ $errors->first() }}
        </div>
    </div>
    <script>
        setTimeout(() => document.getElementById('errorModal')?.remove(), 3000);
    </script>
    @endif

    <script>
        const todayDate = new Date(new Date().toLocaleString("en-US", {
            timeZone: "Asia/Manila"
        }));
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        let currentMonth = todayDate.getMonth();
        let currentYear = todayDate.getFullYear();

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function renderCalendar() {
            const calendarDays = document.getElementById('calendar-days');
            const monthYear = document.getElementById('month-year');
            const eventElements = document.querySelectorAll('#schedule-data .event');
            const events = Array.from(eventElements).map(el => ({
                title: el.dataset.title,
                date: el.dataset.date,
                description: el.dataset.description,
                time: el.dataset.time
            }));

            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const startDay = new Date(currentYear, currentMonth, 1).getDay();
            calendarDays.innerHTML = '';
            monthYear.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            for (let i = 0; i < startDay; i++) calendarDays.innerHTML += `<div class="border"></div>`;
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                const todayMatch = (day === todayDate.getDate() && currentMonth === todayDate.getMonth() && currentYear === todayDate.getFullYear());
                const eventForDay = events.filter(e => e.date === dateStr);

                let box = document.createElement('div');
                box.className = 'border p-1 flex flex-col items-center justify-start cursor-pointer';
                if (todayMatch) box.classList.add('bg-green-400', 'text-black', 'font-bold');
                if (eventForDay.length > 0) box.classList.add('bg-blue-200');
                box.innerHTML = `<span class="font-semibold">${day}</span>`;
                box.onclick = () => openDayView(dateStr, eventForDay);
                calendarDays.appendChild(box);
            }
        }

        function openDayView(date, events) {
            const list = document.getElementById('dayEventList');
            const dateHeader = document.getElementById('dayEventDate');
            const dateObj = new Date(date);
            dateHeader.textContent = dateObj.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            list.innerHTML = '';
            events.sort((a, b) => a.time.localeCompare(b.time)).forEach(e => {
                if (!e.time) return;
                const [hourStr, minuteStr] = e.time.split(':');
                const hour = parseInt(hourStr);
                const minute = parseInt(minuteStr);
                const top = ((hour - 6) * 80) + (minute * (80 / 60));

                const eventBlock = document.createElement('div');
                eventBlock.className = 'absolute bg-blue-500 text-white text-xs p-2 rounded shadow-md';
                eventBlock.style.top = `${top}px`;
                eventBlock.style.left = '0';
                eventBlock.style.right = '0';
                eventBlock.style.height = '35px';
                eventBlock.innerHTML = `<div class="font-semibold cursor-pointer" onclick="showDescription('${e.title}', \`${e.description||'No description.'}\`)">${e.title}</div>`;
                list.appendChild(eventBlock);
            });
            openModal('dayEventModal');
        }

        function showDescription(title, description) {
            document.getElementById('viewTitle').textContent = title;
            document.getElementById('viewDescription').textContent = description;
            openModal('viewDescriptionModal');
        }

        document.getElementById('today').addEventListener('click', () => {
            currentYear = todayDate.getFullYear();
            currentMonth = todayDate.getMonth();
            renderCalendar();
        });
        document.getElementById('prev-month').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });
        document.getElementById('next-month').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });
        document.getElementById('prev-year').addEventListener('click', () => {
            currentYear--;
            renderCalendar();
        });
        document.getElementById('next-year').addEventListener('click', () => {
            currentYear++;
            renderCalendar();
        });
        document.getElementById('search-btn').addEventListener('click', () => {
            const dateInput = document.getElementById('date-picker').value;
            if (!dateInput) {
                alert('Please select a date.');
                return;
            }
            const dateObj = new Date(dateInput);
            currentYear = dateObj.getFullYear();
            currentMonth = dateObj.getMonth();
            renderCalendar();
        });

        renderCalendar();
    </script>
    @endsection