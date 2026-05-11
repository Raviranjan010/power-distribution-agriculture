<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->index(['consumer_id', 'status']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->index(['connection_id', 'status', 'billing_month', 'billing_year']);
        });

        Schema::table('meter_readings', function (Blueprint $table) {
            $table->index(['connection_id', 'reading_date']);
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->index(['consumer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->dropIndex(['consumer_id', 'status']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropIndex(['connection_id', 'status', 'billing_month', 'billing_year']);
        });

        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropIndex(['connection_id', 'reading_date']);
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropIndex(['consumer_id', 'status']);
        });
    }
};
