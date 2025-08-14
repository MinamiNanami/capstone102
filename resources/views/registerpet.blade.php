@extends('layouts.default-layout')
@section('content')

<div class="w-full p-4 overflow-hidden">
    <h1 class="text-3xl font-bold text-gray-700">Registration</h1>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-200 text-green-800 p-3 rounded my-2">
        {{ session('success') }}
    </div>
    @endif

    {{-- Error Messages --}}
    @if ($errors->any())
    <div class="bg-red-200 text-red-800 p-3 rounded my-2">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Registration Form --}}
    <form action="{{ route('registerpet.store') }}" method="POST">
        @csrf

        <div class="bg-gray-100 p-4 rounded mb-6">
            <h2 class="text-xl font-bold text-gray-700 mb-4">Owner's Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input name="owner_name" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Owner's Name" type="text" required />

                <input name="contact_number" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Contact Number" type="number" required />

                <input name="email" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Email Address" type="email" />

                <div class="relative">
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        Date:
                    </span>
                    <input name="registration_date" type="date"
                        class="pl-14 pr-3 py-2 w-full border border-gray-300 rounded bg-white text-black focus:outline-none focus:border-blue-500"
                        required />
                </div>
            </div>

            <input name="address"
                class="w-full border border-gray-300 p-2 rounded bg-white text-black block resize-none h-10"
                placeholder="Enter full address..." type="text" />

            <h2 class="text-xl font-bold text-gray-700 mb-4 mt-6">Pet's Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input name="pet_name" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Pet Name" type="text" required />

                <select name="pet_type" required
                    class="p-2 border border-gray-300 rounded bg-white text-black appearance-none">
                    <option value="" disabled selected>Select Pet Type</option>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                </select>

                <input name="breed" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Breed" type="text" />

                <select name="gender" required
                    class="p-2 border border-gray-300 rounded bg-white text-black appearance-none">
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <div class="relative">
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        Birthday:
                    </span>
                    <input name="birthday" type="date"
                        class="pl-20 pr-3 py-2 w-full border border-gray-300 rounded bg-white text-black focus:outline-none focus:border-blue-500" />
                </div>

                <input name="markings" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Markings" type="text" />

                <input name="disease" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Disease" type="text" />
            </div>

            <textarea name="history"
                class="p-2 border border-gray-300 rounded w-full mb-4 resize-none bg-white text-black"
                placeholder="Enter additional information..." rows="3"></textarea>

            <h2 class="text-xl font-bold text-gray-700 mb-4">Medical Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input name="vital_signs" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Vital Signs" type="text" />

                <input name="diagnosis" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Diagnosis" type="text" />

                <input name="treatment" class="p-2 border border-gray-300 rounded bg-white text-black"
                    placeholder="Treatment" type="text" />
                
                <select name="diagnosed_by" required
                    class="p-2 border border-gray-300 rounded bg-white text-black appearance-none">
                    <option value="" disabled selected> Diagnosed By</option>
                    <option value="Dr. Smith">Dr. 1</option>
                    <option value="Dr. Johnson">Dr. 2</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-yellow-500 text-black px-3 py-1 text-sm rounded hover:bg-yellow-400 transition">
                    ADMIT
                </button>
            </div>
        </div>
    </form>
</div>

@endsection