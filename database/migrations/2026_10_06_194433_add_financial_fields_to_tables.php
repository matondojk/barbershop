<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('commission_type')->default('percentage')->after('roles'); // 'percentage' or 'fixed'
            $table->decimal('commission_value', 8, 2)->default(50.00)->after('commission_type');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 10, 2)->nullable()->after('amount_paid');
            $table->decimal('commission_amount', 10, 2)->nullable()->after('change_amount');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'amount_paid', 'change_amount', 'commission_amount']);
        });
    }
};
