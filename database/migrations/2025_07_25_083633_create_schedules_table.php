<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); //  Laravel needs this as the primary key
            $table->date('date');
            $table->string('title');
            $table->string('customer_name');
            $table->string('phone_number');
            $table->text('description')->nullable();
            $table->time('time');
            $table->date('next_appointment')->nullable();
            $table->timestamps(); //  created_at and updated_at

            $table->unique(['date', 'time'], 'unique_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
