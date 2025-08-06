<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetCheckup extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'pet_inventory_id',
        'disease',
        'diagnosis',
        'vital_signs',
        'treatment',
        'history',
        'timestamps',
    ];

    public function pet()
    {
        return $this->belongsTo(PetInventory::class, 'pet_inventory_id');
    }
}
