<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign keys first, then unique, then columns
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['user_one_id']);
            $table->dropForeign(['user_two_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique('conv_unique');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['user_one_id', 'user_two_id']);
            $table->string('type')->default('direct')->after('id');
            $table->string('name')->nullable()->after('type');
        });

        // 2. Create pivot table
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['conversation_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_user');

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['type', 'name']);
            $table->foreignId('user_one_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two_id')->nullable()->constrained('users')->onDelete('cascade');
        });
    }
};
