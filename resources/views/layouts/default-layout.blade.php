<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" type="image/svg+xml" href="thumbnails/vettrack-logo.svg">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Vettrack
    </title>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/tailwind.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        .dropdown-enter {
            opacity: 0;
            transform: translateY(-10px);
        }

        .dropdown-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.3s, transform 0.3s;
        }

        .dropdown-leave {
            opacity: 1;
            transform: translateY(0);
        }

        .dropdown-leave-active {
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s, transform 0.3s;
        }

        html,
        body {
            overflow-y: hidden;
        }
    </style>
</head>

<body class="bg-blue-100 text-gray-900">
    <div class="flex flex-col md:flex-row h-screen">
        <!-- Sidebar -->
        <div class="hidden md:block w-full md:w-1/4 bg-blue-500 p-4 sticky top-0 h-screen">
            <div class="flex flex-col items-center">
                <img alt="Company Logo" class="mb-4 rounded-full w-24 h-24 md:w-48 md:h-48" height="200"
                    src="{{ asset('images/bussiness-logo.jpg') }}" width="200" />
                <nav class="w-full">
                    <ul class="space-y-4">
                        <li>
                            <a class="flex items-center text-lg font-bold hover:text-blue-700 text-black hover:bg-blue-300 p-2 rounded hover:cursor-pointer hover:bg-blue-300 w-full"
                                href="dashboard">
                                <i class="fas fa-user-plus mr-2">
                                </i>
                                DASHBOARD
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold hover:text-blue-700 text-black hover:bg-blue-300 p-2 rounded hover:cursor-pointer hover:bg-blue-300 w-full"
                                href="registerpet">
                                <i class="fas fa-user-plus mr-2">
                                </i>
                                REGISTER
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="registered">
                                <i class="fas fa-users mr-2">
                                </i>
                                CUSTOMER'S PROFILE
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="pos">
                                <i class="fas fa-cash-register mr-2">
                                </i>
                                POS
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="transactions">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                TRANSACTION
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="schedule">
                                <i class="fas fa-calendar-alt mr-3">
                                </i>
                                SCHEDULE
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="inventory">
                                <i class="fas fa-boxes mr-2">
                                </i>
                                INVENTORY
                            </a>
                        </li>
                        <li class="relative">
                            <button
                                class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                onclick="toggleAdminDropdown()">
                                <i class="fas fa-user mr-2">
                                </i>
                                ADMIN
                                <i class="fas fa-chevron-down ml-auto" id="adminDropdownIcon">
                                </i>
                            </button>
                            <ul class="absolute left-0 mt-2 w-full bg-blue-300 rounded shadow-lg hidden"
                                id="adminDropdown">
                                <li>
                                    <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                        href="profile">
                                        <i class="fas fa-user-cog mr-2">
                                        </i>
                                        Profile Settings
                                    </a>
                                </li>
                                <li class="hidden">
                                    <button
                                        class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                        id="darkModeToggle">
                                        <i class="fas fa-moon mr-2" id="darkModeIcon">
                                        </i>
                                        <span id="darkModeText">
                                            Dark Mode
                                        </span>
                                    </button>
                                </li>
                                <li>
                                    <button
                                        class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                        onclick="openLogoutModal()">
                                        <i class="fas fa-sign-out-alt mr-2">
                                        </i>
                                        Log Out
                                    </button>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- Logout Modal -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden" id="logoutModal">
            <div class="bg-white p-6 rounded-md shadow-md">
                <h2 class="text-xl font-bold mb-4">
                    Are you sure you want to log out?
                </h2>
                <div class="flex justify-end space-x-4">
                    <button
                        class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400"
                        onclick="closeLogoutModal()">
                        Cancel
                    </button>
                    <!-- Log Out Button -->
                    <button class="bg-red-500 text-black px-4 py-2 rounded hover:bg-red-400"
                        onclick="document.getElementById('logoutForm').submit()">
                        Log Out
                    </button>

                    <!-- Hidden logout form -->
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        <!-- Mobile Sidebar -->
        <div class="fixed top-0 left-0 h-full w-0 bg-blue-500 overflow-x-hidden z-50" id="mySidenav">
            <a class="absolute top-0 right-0 mt-4 mr-4 text-3xl" href="javascript:void(0)" onclick="closeNav()">
                ×
            </a>
            <div class="flex flex-col items-center p-3">
                <img alt="Company Logo" class="mb-4 rounded-full w-24 h-24 md:w-48 md:h-48" height="200"
                    src="{{ asset('images/bussiness-logo.jpg') }}" width="200" />
                <nav class="w-full">
                    <ul class="space-y-4">
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer hover:bg-blue-300 w-full"
                                href="dashboard">
                                <i class="fas fa-user-plus mr-2">
                                </i>
                                DASHBOARD
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer hover:bg-blue-300 w-full"
                                href="registerPet">
                                <i class="fas fa-user-plus mr-2">
                                </i>
                                REGISTER
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="registered">
                                <i class="fas fa-users mr-2">
                                </i>
                                CUSTOMER'S RECORD
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="pos">
                                <i class="fas fa-cash-register mr-2">
                                </i>
                                POS
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="transaction">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                TRANSACTION
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                href="schedule">
                                <i class="fas fa-calendar-alt mr-3">
                                </i>
                                SCHEDULE
                            </a>
                        </li>
                        <li onclick="location.href='inventory'">
                            <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full">
                                <i class="fas fa-boxes mr-2">
                                </i>
                                INVENTORY
                            </a>
                        </li>
                        <li class="relative">
                            <button
                                class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                onclick="toggleAdminDropdownMobile()">
                                <i class="fas fa-user-cog mr-2">
                                </i>
                                ADMIN
                                <i class="fas fa-chevron-down ml-auto" id="adminDropdownIconMobile">
                                </i>
                            </button>
                            <ul class="absolute left-0 mt-2 w-full bg-blue-300 rounded shadow-lg hidden"
                                id="adminDropdownMobile">
                                <li>
                                    <a class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                        href="profile">
                                        <i class="fas fa-user-cog mr-2">
                                        </i>
                                        Profile Settings
                                    </a>
                                </li>
                                <li class="hidden">
                                    <button
                                        class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                        id="darkModeToggleMobile">
                                        <i class="fas fa-moon mr-2" id="darkModeIconMobile">
                                        </i>
                                        <span id="darkModeTextMobile">
                                            Dark Mode
                                        </span>
                                    </button>
                                </li>
                                <li>
                                    <button
                                        class="flex items-center text-lg font-bold text-black hover:text-blue-700 hover:bg-blue-300 p-2 rounded hover:cursor-pointer w-full"
                                        onclick="openLogoutModal()">
                                        <i class="fas fa-sign-out-alt mr-2">
                                        </i>
                                        Log Out
                                    </button>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Dark Mode Friendly Loading Buffer -->
        <div id="loader"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-white transition-opacity duration-300">
            <div
                class="w-12 h-12 border-4 border-gray-300 border-t-gray-800 rounded-full animate-spin">
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="sticky top-0 bg-blue-500 z-10 h-12 flex items-center" id="stickyHeader">
            <div class="flex justify-between items-center h-12">
                <div class="flex items-center h-12">
                    <span
                        class="bg-blue-500 p-2 rounded-md text-3xl cursor-pointer md:hidden mr-4 h-12 flex items-center"
                        onclick="openNav()">☰</span>
                </div>
            </div>
        </div>
        <div class="w-full p-3">

            <!-- Main content goes here -->
            <div class="bg-gray-100 rounded-md">
                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>