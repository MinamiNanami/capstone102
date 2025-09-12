<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'total',
        'pos_sale_id',
        'service',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // ✅ Add this relation
    public function posSale()
    {
        return $this->belongsTo(PosSale::class);
    }
}
