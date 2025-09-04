<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pet_inventory', function (Blueprint $table) {
            $table->id();

            // Owner Info
            $table->string('owner_name');
            $table->string('contact_number');
            $table->string('email')->nullable();
            $table->date('registration_date');
            $table->text('address')->nullable();

            // Pet Info
            $table->string('pet_name');
            $table->string('pet_type');
            $table->string('breed')->nullable();
            $table->string('gender');
            $table->date('birthday')->nullable();
            $table->string('markings')->nullable();
            $table->text('history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_inventory');
    }
};
