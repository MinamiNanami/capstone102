<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetCheckup extends Model
{
    protected $fillable = [
        'pet_inventory_id',
        'date',
        'next_appointment',
        'disease',
        'diagnosis',
        'vital_signs',
        'treatment',
        'diagnosed_by'
    ];

    public function pet()
    {
        return $this->belongsTo(PetInventory::class, 'pet_inventory_id');
    }
}
