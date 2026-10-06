<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add work hours to users
        Schema::table('users', function (Blueprint $table) {
            $table->time('work_start_time')->default('08:00:00')->after('is_active');
            $table->time('work_end_time')->default('19:30:00')->after('work_start_time');
        });

        // 2. Create customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp')->unique();
            $table->timestamps();
        });

        // 3. Add customer_id to appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });

        Schema::dropIfExists('customers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['work_start_time', 'work_end_time']);
        });
    }
};
