<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->string('insurance_company')->nullable()->after('extras');
            $table->string('insurance_policy_number')->nullable()->after('insurance_company');
            $table->date('insurance_expires_at')->nullable()->after('insurance_policy_number');
            $table->date('itv_expires_at')->nullable()->after('insurance_expires_at');
            $table->date('itv_last_passed_at')->nullable()->after('itv_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_company',
                'insurance_policy_number',
                'insurance_expires_at',
                'itv_expires_at',
                'itv_last_passed_at',
            ]);
        });
    }
};
