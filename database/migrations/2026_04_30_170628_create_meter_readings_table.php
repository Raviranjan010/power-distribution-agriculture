<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('connections')->cascadeOnDelete();
            $table->foreignId('lineman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('reading_date');
            $table->decimal('previous_reading', 10, 2)->default(0);
            $table->decimal('current_reading', 10, 2);
            $table->decimal('units_consumed', 10, 2);
            $table->boolean('is_verified')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
