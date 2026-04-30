<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('grv_number')->unique();
            $table->foreignId('consumer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('connection_id')->nullable()->constrained('connections')->cascadeOnDelete();
            $table->enum('complaint_type', ['voltage_fluctuation', 'no_supply', 'meter_fault', 'billing_error', 'transformer_issue', 'other']);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['filed', 'assigned', 'in_review', 'resolved', 'closed'])->default('filed');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_remarks')->nullable();
            $table->timestamp('filed_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
