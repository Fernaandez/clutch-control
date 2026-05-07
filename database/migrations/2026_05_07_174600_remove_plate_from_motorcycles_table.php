<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('motorcycles', 'plate')) {
            return;
        }

        // Drop unique index if it exists (uses default Laravel naming convention)
        $indexes = collect(DB::select("SHOW INDEX FROM motorcycles"))
            ->pluck('Key_name')
            ->unique()
            ->toArray();

        Schema::table('motorcycles', function (Blueprint $table) use ($indexes) {
            if (in_array('motorcycles_plate_unique', $indexes, true)) {
                $table->dropUnique('motorcycles_plate_unique');
            }
            $table->dropColumn('plate');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('motorcycles', 'plate')) {
            Schema::table('motorcycles', function (Blueprint $table) {
                $table->string('plate')->nullable();
            });
        }
    }
};
