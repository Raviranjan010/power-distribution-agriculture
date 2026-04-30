<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subsidy_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_name');
            $table->text('description')->nullable();
            $table->json('eligibility_criteria')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('max_units_covered', 8, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subsidy_schemes');
    }
};
