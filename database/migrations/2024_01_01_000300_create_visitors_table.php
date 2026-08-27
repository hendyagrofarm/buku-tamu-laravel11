<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30)->unique();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->string('identity_number', 100)->nullable();
            $table->string('vehicle_number', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('visitors'); }
};
