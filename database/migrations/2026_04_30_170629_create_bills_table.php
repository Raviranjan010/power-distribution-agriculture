<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->foreignId('connection_id')->constrained('connections')->cascadeOnDelete();
            $table->foreignId('meter_reading_id')->nullable()->constrained('meter_readings')->nullOnDelete();
            $table->integer('billing_month');
            $table->integer('billing_year');
            $table->decimal('units_consumed', 10, 2);
            $table->decimal('rate_per_unit', 8, 2);
            $table->decimal('energy_charges', 10, 2);
            $table->decimal('fixed_charges', 10, 2);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('subsidy_amount', 10, 2)->default(0);
            $table->decimal('net_payable', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue', 'partially_paid'])->default('pending');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
