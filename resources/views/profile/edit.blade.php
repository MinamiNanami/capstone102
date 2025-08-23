@extends('layouts.default-layout')

@section('content')
<div class="flex h-screen">
    <!-- Right side content wrapper -->
    <div class="flex-1 overflow-y-auto bg-gray-100 font-roboto">
        <div class="container mx-auto p-6 space-y-6">

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-800">
                    Profile Settings
                </h1>
                <a href="{{ route('register') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    Register
                </a>
            </div>

            <!-- Update Profile Information -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-700">
                    Update Profile Information
                </h2>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-700">
                    Change Password
                </h2>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-red-300">
                <h2 class="text-xl font-bold mb-4 text-red-600">
                    Danger Zone
                </h2>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
