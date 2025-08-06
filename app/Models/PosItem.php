<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosItem extends Model
{
    protected $fillable = ['pos_sale_id', 'inventory_item_id', 'quantity', 'amount'];

    public function sale()
    {
        return $this->belongsTo(PosSale::class);
    }

    public function product()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}