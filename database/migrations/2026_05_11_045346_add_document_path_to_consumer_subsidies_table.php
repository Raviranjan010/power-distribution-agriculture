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
        Schema::table('consumer_subsidies', function (Blueprint $table) {
            $table->string('document_path')->nullable()->after('scheme_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumer_subsidies', function (Blueprint $table) {
            $table->dropColumn('document_path');
        });
    }
};
