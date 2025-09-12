<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();   // Customer name
            $table->string('service')->nullable();         // ✅ Service type (Check-up, Surgery, etc.)
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0); // ✅ % discount
            $table->decimal('total', 10, 2)->default(0);   // ✅ final total
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
