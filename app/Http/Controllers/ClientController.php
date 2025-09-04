<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PetInventory;

class ClientController extends Controller
{
    public function index()
    {
        $pets = PetInventory::orderBy('updated_at', 'desc')->get();
        return view('registered', compact('pets'));
    }

    public function updateCheckup(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pet_inventory,id',
            'disease' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'vital_signs' => 'nullable|string',
            'treatment' => 'nullable|string',
            'history' => 'nullable|string',
            'next_appointment' => 'nullable|date',
        ]);

        $pet = PetInventory::findOrFail($request->id);

        $pet->disease = $request->disease;
        $pet->diagnosis = $request->diagnosis;
        $pet->vital_signs = $request->vital_signs;
        $pet->treatment = $request->treatment;
        $pet->history = $request->history;
        $pet->save();

        return redirect()->back()->with('success', 'Check-up record updated!');
    }
}
