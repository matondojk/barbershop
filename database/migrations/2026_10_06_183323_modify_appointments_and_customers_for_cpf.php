<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('cpf')->nullable()->unique()->after('name');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_whatsapp']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('customer_name')->nullable();
            $table->string('customer_whatsapp')->nullable();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
};
