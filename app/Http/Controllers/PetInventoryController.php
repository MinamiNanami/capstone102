<?php

namespace App\Http\Controllers;

use App\Models\PetCheckup;
use Illuminate\Http\Request;
use App\Models\PetInventory;

class PetInventoryController extends Controller
{
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
            'disease' => 'nullable|string',
            'history' => 'nullable|string',
            'pet_name' => 'required|string',
            'diagnosis' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'treatment' => 'nullable|string',
        ]);

        PetInventory::create($request->all());

        return back()->with('success', 'Pet registered successfully!');
    }

    public function showPatients(Request $request)
    {
        $query = PetInventory::query();

        // Search logic
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('owner_name', 'like', "%$search%")
                  ->orWhere('pet_name', 'like', "%$search%");
            });
        }

        // Sort logic
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

        $patients = $query->with('checkups')->get();

        return view('registered', compact('patients'));
    }

    public function index()
    {
        $pets = PetInventory::all();
        return view('inventory.index', compact('pets'));
    }

    public function storeCheckup(Request $request)
    {
        $request->validate([
            'pet_inventory_id' => 'required|exists:pet_inventory,id',
            'disease' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'treatment' => 'nullable|string',
            'history' => 'nullable|string',
        ]);

        PetCheckup::create($request->all());

        return redirect()->route('registered')->with('success', 'Check-up added!');
    }

}
