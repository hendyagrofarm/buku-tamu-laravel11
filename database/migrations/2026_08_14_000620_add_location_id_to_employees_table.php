<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('division_id')->constrained('locations')->nullOnDelete();
            $table->index(['location_id', 'is_active']);
        });

        $pabrik = DB::table('locations')->where('slug', 'pabrik')->value('id');
        if ($pabrik) {
            DB::table('employees')->whereNull('location_id')->update(['location_id' => $pabrik]);
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropIndex(['location_id', 'is_active']);
            $table->dropColumn('location_id');
        });
    }
};
