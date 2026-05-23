<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('usage_logs');
    }

    public function down(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained()->onDelete('cascade');
            $table->date('log_date');
            $table->decimal('units_consumed', 10, 2);
            $table->timestamps();
        });
    }
};
