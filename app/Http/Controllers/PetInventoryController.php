<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PetInventory;
use App\Models\PetCheckup;
use App\Models\Schedule;

class PetInventoryController extends Controller
{
    // 🐾 Register a new pet
    public function store(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string',
            'contact_number' => 'required|string',
            'email' => 'nullable|email',
            'registration_date' => 'required|date',
            'address' => 'nullable|string',
            'pet_type' => 'required|string',
            'breed' => 'nullable|string',
            'gender' => 'required|string',
            'birthday' => 'nullable|date',
            'markings' => 'nullable|string',
            'history' => 'nullable|string',
            'pet_name' => 'required|string',
        ]);

        PetInventory::create($request->all());

        return back()->with('success', 'Pet registered successfully!');
    }

    // 🐾 Show all patients with search + sort
    public function showPatients(Request $request)
    {
        $query = PetInventory::query();

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('owner_name', 'like', "%$search%")
                  ->orWhere('pet_name', 'like', "%$search%");
            });
        }

        // ↕️ Sort
        switch ($request->input('sort')) {
            case 'name_asc':
                $query->orderBy('pet_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('pet_name', 'desc');
                break;
            case 'date_latest':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'date_oldest':
                $query->orderBy('updated_at', 'asc');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        // ⚡ Eager load checkups
        $patients = $query->with('checkups')->get();

        return view('registered', compact('patients'));
    }

    // 🐾 Dashboard index
    public function index()
    {
        $schedules = Schedule::all();
        $checkups = PetCheckup::with('pet')->get(); // include pet name

        return view('registered', compact('schedules', 'checkups'));
    }

    // 🐾 Store new checkup (with schedule integration)
    public function storeCheckup(Request $request)
    {
        $request->validate([
            'pet_inventory_id' => 'required|exists:pet_inventory,id', // must match your table
            'date' => 'required|date',
            'next_appointment' => 'nullable|date|after_or_equal:date',
            'disease' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'treatment' => 'nullable|string',
            'diagnosed_by' => 'nullable|string',
        ]);

        // ✅ Save the checkup
        $checkup = PetCheckup::create($request->all());

        // ✅ If next appointment is set, also create a schedule
        if ($request->filled('next_appointment')) {
            $patient = PetInventory::findOrFail($request->pet_inventory_id);

            Schedule::create([
                'date' => $request->next_appointment,
                'time' => '09:00:00', // default time (can be improved with a time input in the form)
                'title' => 'Follow-up Appointment',
                'customer_name' => $patient->owner_name,
                'phone_number' => $patient->contact_number,
                'description' => "Follow-up for pet {$patient->pet_name}, diagnosed with {$request->disease}",
                'next_appointment' => null, // optional
            ]);
        }

        return redirect()->route('registered')->with('success', 'Check-up added!');
    }

    // 🐾 Store freeform medical history notes
    public function storeHistory(Request $request)
    {
        $request->validate([
            'pet_inventory_id' => 'required|exists:pet_inventory,id',
            'history' => 'required|string',
        ]);

        $patient = PetInventory::findOrFail($request->pet_inventory_id);

        // Append note with timestamp
        $newNote = now()->format('Y-m-d H:i') . " - " . $request->history;

        $patient->history = trim(($patient->history ?? '') . "\n\n" . $newNote);
        $patient->save();

        return redirect()->back()->with('success', 'Medical history added successfully.');
    }
}
