<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('thumbnails/vettrack-logo.svg') }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Vettrack</title>

    <script src="{{ asset('js/tailwind.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            }
        }
    </script>
</head>

<body class="flex items-center justify-center min-h-screen bg-blue-500 md:bg-gray-200">
    <div class="flex w-full h-screen flex-col md:flex-row">
        <!-- Left Side -->
        <div class="w-full md:w-1/2 bg-blue-500 p-8 flex flex-col justify-center items-center h-full">
            <div class="flex justify-center mb-6 w-3/4">
                <div class="flex items-center w-full justify-center rounded-md">
                    <i class="fas fa-paw text-5xl md:text-7xl text-black mr-3"></i>
                    <span class="text-black text-4xl md:text-6xl font-bold">
                        VET-TRACK
                    </span>
                </div>
            </div>

            <!-- Functional Laravel Breeze Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4 w-3/4">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input
                        class="w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        id="email" name="email" type="email" value="{{ old('email') }}"
                        placeholder="Please enter your email" required autofocus />
                    @error('email')
                    <p class="text-sm text-red-200 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6 relative">
                    <label class="block text-white text-sm font-bold mb-2" for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input
                        class="w-full p-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        id="password" name="password" type="password" placeholder="Please enter password" required />
                    <i class="fas fa-eye-slash absolute right-3 top-10 cursor-pointer" id="password-toggle"
                        onclick="togglePasswordVisibility()"></i>
                    @error('password')
                    <p class="text-sm text-red-200 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex justify-between items-center">
                    <label class="flex items-center text-white">
                        <input class="mr-2" type="checkbox" name="remember" />
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                    <a class="text-white text-sm underline hover:text-gray-300"
                        href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button class="w-full py-2 bg-blue-800 text-white rounded-md hover:bg-blue-700" type="submit">
                    LOG IN
                </button>
            </form>
        </div>

        <!-- Right Side -->
        <div class="w-full md:w-1/2 relative hidden md:block">
            <img alt="Background with a clock and a logo" class="absolute inset-0 w-full h-full object-cover"
                src="{{ asset('images/dog-cat-logo.png') }}" />
        </div>
    </div>
</body>

</html>