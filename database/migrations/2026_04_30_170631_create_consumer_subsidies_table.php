<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumer_subsidies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('scheme_id')->constrained('subsidy_schemes')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['applied', 'approved', 'rejected'])->default('applied');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumer_subsidies');
    }
};
