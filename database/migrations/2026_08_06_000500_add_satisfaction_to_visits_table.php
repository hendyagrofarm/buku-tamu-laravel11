<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->unsignedTinyInteger('satisfaction_rating')->nullable()->after('status');
            $table->timestamp('surveyed_at')->nullable()->after('satisfaction_rating');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['satisfaction_rating', 'surveyed_at']);
        });
    }
};
