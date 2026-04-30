<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->enum('role', ['farmer', 'lineman', 'sdo', 'admin'])->default('farmer');
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->string('farmer_id_number')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
