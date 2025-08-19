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
        Schema::create('pet_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_inventory_id');
            $table->string('disease')->nullable();
            $table->string('diagnosis')->nullable();
            $table->string('vital_signs')->nullable();
            $table->string('treatment')->nullable();
            $table->string('diagnosed_by')->nullable();
            $table->text('history')->nullable();
            $table->timestamps();

            $table->foreign('pet_inventory_id')->references('id')->on('pet_inventory')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_checkups');
    }
};
