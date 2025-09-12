<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    public $timestamps = false;

    protected $fillable = ['title', 'customer_name', 'phone_number', 'date', 'description', 'time', 'next_appointment']; // Make sure these match your DB
}
