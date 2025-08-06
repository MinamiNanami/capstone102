<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'service',
        'service_fee',
        'discount',
        'total',
    ];

    public function items()
    {
        return $this->hasMany(PosItem::class);
    }

    // ✅ Add this to link back to the transaction (if you have pos_sale_id in the transactions table)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
