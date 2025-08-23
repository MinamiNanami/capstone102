@extends('layouts.default-layout')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Enter OTP</h2>

        @if(session('success'))
            <div class="text-green-600 mb-2">{{ session('success') }}</div>
        @endif

        @error('otp')
            <div class="text-red-600 mb-2">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf
            <div class="mb-4">
                <label for="otp" class="block text-gray-700">One-Time Password</label>
                <input type="text" name="otp" id="otp" class="w-full p-2 border rounded" maxlength="6" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Verify</button>
        </form>
    </div>
</div>
@endsection