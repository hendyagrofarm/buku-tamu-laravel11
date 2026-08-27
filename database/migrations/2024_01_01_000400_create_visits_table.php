<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_number', 30)->unique();
            $table->foreignId('visitor_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('purpose');
            $table->unsignedTinyInteger('number_of_people')->default(1);
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'check_in_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('visits'); }
};
