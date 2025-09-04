<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetInventory extends Model
{

    protected $table = 'pet_inventory';
        
    protected $fillable = [
        'owner_name',
        'pet_name',
        'pet_type',
        'gender',
        'breed',
        'birthday',
        'markings',
        'contact_number',
        'email',
        'address',
        'registration_date',
        'history',
    ];

    public function checkups()
    {
        return $this->hasMany(PetCheckup::class, 'pet_inventory_id'); // ✅ reference PetCheckup
    }
}
