<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate_per_unit', 8, 2);
            $table->decimal('fixed_charge_per_kw', 8, 2);
            $table->enum('applicable_to', ['agricultural', 'domestic', 'commercial']);
            $table->date('effective_from');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_categories');
    }
};
