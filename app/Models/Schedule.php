<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    public $timestamps = false;
    
    protected $fillable = ['title', 'date', 'description', 'time']; // Make sure these match your DB

    protected $casts = [
        'date' => 'datetime',
    ];
}

