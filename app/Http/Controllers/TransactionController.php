<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\InventoryItem;
use App\Models\PosSale;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['items', 'posSale'])->orderBy('created_at', 'desc')->get();
        return view('transactions', compact('transactions'));
    }

    public function completePurchase(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'service_fee' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Create the POS sale
        $posSale = PosSale::create([
            'customer_name' => $validated['customer_name'],
            'service_fee' => $validated['service_fee'],
            'discount' => $validated['discount'],
            'total' => $validated['total'],
        ]);

        // Create the transaction linked to posSale
        $transaction = Transaction::create([
            'pos_sale_id' => $posSale->id,
            'customer_name' => $validated['customer_name'],
            'total' => $validated['total'],
        ]);

        foreach ($validated['items'] as $item) {
            $product = InventoryItem::find($item['inventory_item_id']);

            // Save to transaction_items
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'inventory_item_id' => $product->id,
                'item_name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Deduct stock
            $product->decrement('quantity', $item['quantity']);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction completed successfully!');
    }
}
