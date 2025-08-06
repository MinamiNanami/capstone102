<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetInventory extends Model
{
    use HasFactory;

    protected $table = 'pet_inventory';

    protected $fillable = [
        'owner_name',
        'contact_number',
        'email',
        'registration_date',
        'address',
        'pet_name',
        'pet_type',
        'breed',
        'gender',
        'birthday',
        'markings',
        'disease',
        'history',
        'diagnosis',
        'vital_signs',
        'treatment'
    ];

    public function checkups()
    {
        return $this->hasMany(\App\Models\PetCheckup::class, 'pet_inventory_id');
    }
}
