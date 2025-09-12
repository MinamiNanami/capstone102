<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::all();
        return view('inventory', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_items,name',
            'category' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'expiration_date' => 'nullable|string', // input type=month gives YYYY-MM
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Convert YYYY-MM into YYYY-MM-01
        if (!empty($validated['expiration_date'])) {
            $validated['expiration_date'] = $validated['expiration_date'] . '-01';
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('inventory', 'public');
        }

        InventoryItem::create($validated);

        return redirect()->back()->with('success', 'Item added successfully.');
    }

    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'expiration_date' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (!empty($validated['expiration_date'])) {
            $validated['expiration_date'] = $validated['expiration_date'] . '-01';
        }

        if ($request->hasFile('image')) {
            // delete old image
            if ($item->image && Storage::exists('public/' . $item->image)) {
                Storage::delete('public/' . $item->image);
            }
            $validated['image'] = $request->file('image')->store('inventory', 'public');
        }

        $item->update($validated);

        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        $item = InventoryItem::findOrFail($id);

        if ($item->image && Storage::exists('public/' . $item->image)) {
            Storage::delete('public/' . $item->image);
        }

        $item->delete();

        return redirect()->route('inventory.index')->with('success', 'Item deleted successfully.');
    }
}
