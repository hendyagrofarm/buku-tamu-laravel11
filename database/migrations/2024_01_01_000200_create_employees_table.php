<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('position');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['division_id', 'is_active']);
        });
    }

    public function down(): void { Schema::dropIfExists('employees'); }
};
