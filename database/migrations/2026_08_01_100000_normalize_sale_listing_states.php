<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Els estats es guardaven com a text lliure ('actiu (reservat) (nou)').
     * Els normalitzem a: actiu | reservat | venuda | pausat.
     */
    public function up(): void
    {
        DB::table('sale_listings')
            ->where('state', 'actiu (reservat) (nou)')
            ->update(['state' => 'reservat']);

        DB::table('sale_listings')
            ->whereNotIn('state', ['actiu', 'reservat', 'venuda', 'pausat'])
            ->update(['state' => 'actiu']);

        Schema::table('sale_listings', function (Blueprint $table) {
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::table('sale_listings', function (Blueprint $table) {
            $table->dropIndex(['state']);
        });

        DB::table('sale_listings')
            ->where('state', 'reservat')
            ->update(['state' => 'actiu (reservat) (nou)']);

        DB::table('sale_listings')
            ->where('state', 'pausat')
            ->update(['state' => 'actiu']);
    }
};
