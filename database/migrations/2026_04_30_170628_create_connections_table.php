<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->string('connection_number')->unique();
            $table->foreignId('consumer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('connection_type', ['tubewell_pump', 'irrigation_motor', 'thresher', 'drip_irrigation']);
            $table->string('field_name')->nullable();
            $table->decimal('sanctioned_load_kw', 8, 2);
            $table->string('meter_number')->nullable()->unique();
            $table->foreignId('tariff_category_id')->nullable()->constrained('tariff_categories')->nullOnDelete();
            $table->enum('status', ['active', 'disconnected', 'pending'])->default('pending');
            $table->date('installation_date')->nullable();
            $table->foreignId('sdo_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
