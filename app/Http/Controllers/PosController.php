<?php

namespace App\Http\Controllers;

use App\Models\PosSale;
use App\Models\PosItem;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\Transaction;
use App\Models\TransactionItem;

class PosController extends Controller
{
    public function index()
    {
        $items = InventoryItem::all();
        return view('pos', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'service_fee' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric',
        ]);

        // 1. Save to POS table
        $sale = PosSale::create([
            'customer_name' => $request->customer_name,
            'service' => $request->service,
            'service_fee' => $request->service_fee ?? 0,
            'discount' => $request->discount ?? 0,
            'total' => $request->total,
        ]);

        foreach ($request->items as $item) {
            PosItem::create([
                'pos_sale_id' => $sale->id,
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
            ]);
        }

        // 2. Deduct inventory stock
        foreach ($request->items as $item) {
            $inventory = InventoryItem::find($item['inventory_item_id']);
            if ($inventory) {
                $inventory->quantity -= $item['quantity'];
                $inventory->save();
            }
        }

        // 3. Save to Transactions table with receipt number
        $transaction = Transaction::create([
            'customer_name' => $request->customer_name ?? 'Customer',
            'amount' => $request->total,
        ]);

        // Generate a unique, sequential receipt number like "TX-000001"
        $transaction->receipt_number = 'TX-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
        $transaction->save();

        foreach ($request->items as $item) {
            $product = InventoryItem::find($item['inventory_item_id']);
            $transaction->items()->create([
                'item_name' => $product->name ?? 'Unknown Item',
                'quantity' => $item['quantity'],
                'price' => $item['amount'],
            ]);
        }

        // Pass transaction to the session for the receipt
        return redirect()->back()
            ->with('success', 'Sale and transaction recorded successfully.')
            ->with('transaction', $transaction);
    }


    public function completePurchase(Request $request)
    {
        $transaction = Transaction::create([
            'customer_name' => $request->customer_name,
            'amount' => $request->total,
            // 'created_at' handled automatically
        ]);

        foreach ($request->items as $item) {
            $transaction->items()->create([
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return redirect()->route('transactions.index');
    }
}
