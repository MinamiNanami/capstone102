@extends('layouts.default-layout')
@section('content')

<style>
    html, body {
        overflow: hidden;
        height: 100%;
    }
</style>

<!-- Sticky Header -->
<div class="sticky top-0 bg-blue-500 dark:bg-gray-800 z-10 h-12 flex items-center" id="stickyHeader">
    <div class="flex justify-between items-center h-12 w-full px-4">

        <!-- Left: menu icon -->
        <div class="flex items-center h-12">
            <span class="bg-blue-500 dark:bg-gray-800 p-2 rounded-md text-3xl cursor-pointer md:hidden h-12 flex items-center" onclick="openNav()">☰</span>
        </div>

        <!-- Right: date input, search, and add -->
        <div class="flex items-center space-x-2">
            <input class="hidden border border-gray-300 dark:border-gray-700 rounded px-2 py-1" id="date-picker" type="date" />
            <button class="text-white text-xl" id="search-btn" title="Search by Date">
                <i class="fas fa-search"></i>
            </button>
            <button onclick="openModal('addEventModal')" class="text-white text-xl" title="Add Event">
                <i class="fas fa-plus"></i>
            </button>
        </div>

    </div>
</div>

<div class="w-full">
    <div class="max-w-full h-full mx-auto p-4 flex flex-col">
        <div class="flex flex-wrap items-center mb-4 space-x-2">
            <button class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-full px-4 py-2 text-gray-700 dark:text-gray-300" id="today">Today</button>

            <button class="text-gray-500 dark:text-gray-300" id="prev-year" title="Previous Year"><i class="fas fa-angle-double-left"></i></button>
            <button class="text-gray-500 dark:text-gray-300" id="prev-month" title="Previous Month"><i class="fas fa-chevron-left"></i></button>

            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100" id="month-year">Month Year</h2>

            <button class="text-gray-500 dark:text-gray-300" id="next-month" title="Next Month"><i class="fas fa-chevron-right"></i></button>
            <button class="text-gray-500 dark:text-gray-300" id="next-year" title="Next Year"><i class="fas fa-angle-double-right"></i></button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex-grow flex flex-col">
            <div class="grid grid-cols-7 text-center text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700">
                <div class="py-2">SUN</div>
                <div class="py-2">MON</div>
                <div class="py-2">TUE</div>
                <div class="py-2">WED</div>
                <div class="py-2">THU</div>
                <div class="py-2">FRI</div>
                <div class="py-2">SAT</div>
            </div>
            <div class="grid grid-cols-7 text-center border-b border-gray-200 dark:border-gray-700 flex-grow text-sm md:text-base" id="calendar-days"></div>
        </div>
    </div>

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

    <!-- Add Event Modal -->
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
            <div class="relative border-l border-gray-300 h-[780px]" id="timeline">
                @for ($hour = 6; $hour <= 18; $hour++)
                    <div class="absolute left-0 flex items-center w-full text-xs text-gray-500" style="top: {{ ($hour - 6) * 60 }}px; height: 60px;">
                        <div class="w-24 text-right pr-2 font-semibold">
                            {{ ($hour % 12 === 0 ? 12 : $hour % 12) }}:00 {{ $hour >= 12 ? 'PM' : 'AM' }}
                        </div>
                        <div class="border-t border-dashed border-gray-300 flex-grow ml-2"></div>
                    </div>
                @endfor
                <div id="dayEventList" class="absolute left-[7.5rem] right-4 top-0"></div>
            </div>
        </div>
    </div>

    <!-- View Description Modal -->
    <div id="viewDescriptionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold" id="viewTitle">Event Details</h2>
                <button onclick="closeModal('viewDescriptionModal')" class="text-gray-500 hover:text-red-500 text-2xl">&times;</button>
            </div>
            <p class="text-gray-700 whitespace-pre-wrap" id="viewDescription"></p>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const todayDate = new Date(new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" }));
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        let currentMonth = todayDate.getMonth();
        let currentYear = todayDate.getFullYear();

        function formatTo12Hour(timeStr) {
            if (!timeStr) return 'N/A';
            const [hour, minute] = timeStr.split(':');
            const h = parseInt(hour);
            const suffix = h >= 12 ? 'PM' : 'AM';
            const formattedHour = (h % 12 === 0 ? 12 : h % 12);
            return `${formattedHour}:${minute} ${suffix}`;
        }

        function timeToOffsetMinutes(timeStr) {
            const [h, m] = timeStr.split(':');
            return (parseInt(h) - 6) * 60 + parseInt(m);
        }

        function openDayView(date, events) {
            const list = document.getElementById('dayEventList');
            const dateHeader = document.getElementById('dayEventDate');
            const dateObj = new Date(date);
            const formatted = dateObj.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            dateHeader.textContent = formatted;
            list.innerHTML = '';

            const sorted = events.filter(e => e.time).sort((a, b) => a.time.localeCompare(b.time));
            sorted.forEach(e => {
                const offset = timeToOffsetMinutes(e.time);
                const eventBlock = document.createElement('div');
                eventBlock.className = 'absolute bg-blue-500 text-white text-xs p-2 rounded shadow-md';
                eventBlock.style.top = `${offset}px`;
                eventBlock.style.left = '0';
                eventBlock.style.right = '0';
                eventBlock.style.height = '40px';
                eventBlock.innerHTML = `
                    <div class="font-semibold cursor-pointer" onclick="showDescription('${e.title}', \`${e.description || 'No description.'}\`)">
                        ${e.title}
                    </div>
                `;
                list.appendChild(eventBlock);
            });

            openModal('dayEventModal');
        }

        function showDescription(title, description) {
            document.getElementById('viewTitle').textContent = title;
            document.getElementById('viewDescription').textContent = description;
            openModal('viewDescriptionModal');
        }

        function getDaysInMonth(year, month) {
            return new Date(year, month + 1, 0).getDate();
        }

        function getStartDay(year, month) {
            return new Date(year, month, 1).getDay();
        }

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

            const daysInMonth = getDaysInMonth(currentYear, currentMonth);
            const startDay = getStartDay(currentYear, currentMonth);
            calendarDays.innerHTML = '';
            monthYear.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            for (let i = 0; i < startDay; i++) {
                calendarDays.innerHTML += `<div class="border"></div>`;
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const todayMatch = (
                    day === todayDate.getDate() &&
                    currentMonth === todayDate.getMonth() &&
                    currentYear === todayDate.getFullYear()
                );

                const eventForDay = events.filter(e => e.date === dateStr);
                let box = document.createElement('div');
                box.className = 'border p-1 flex flex-col items-center justify-start cursor-pointer';
                if (todayMatch) box.classList.add('bg-green-400', 'text-black', 'font-bold');

                box.innerHTML = `<span class="font-semibold">${day}</span>`;
                box.onclick = () => openDayView(dateStr, eventForDay);
                calendarDays.appendChild(box);
            }
        }

        document.getElementById('prev-month').onclick = () => {
            currentMonth = (currentMonth === 0 ? 11 : currentMonth - 1);
            if (currentMonth === 11) currentYear--;
            renderCalendar();
        };
        document.getElementById('next-month').onclick = () => {
            currentMonth = (currentMonth === 11 ? 0 : currentMonth + 1);
            if (currentMonth === 0) currentYear++;
            renderCalendar();
        };
        document.getElementById('prev-year').onclick = () => {
            currentYear--;
            renderCalendar();
        };
        document.getElementById('next-year').onclick = () => {
            currentYear++;
            renderCalendar();
        };
        document.getElementById('today').onclick = () => {
            currentMonth = todayDate.getMonth();
            currentYear = todayDate.getFullYear();
            renderCalendar();
        };

        document.getElementById('search-btn').onclick = () => {
            document.getElementById('date-picker').classList.toggle('hidden');
        };

        document.getElementById('date-picker').addEventListener('change', function (e) {
            const selected = new Date(e.target.value);
            currentMonth = selected.getMonth();
            currentYear = selected.getFullYear();
            renderCalendar();
        });

        window.addEventListener('DOMContentLoaded', renderCalendar);
    </script>
</div>

@endsection
