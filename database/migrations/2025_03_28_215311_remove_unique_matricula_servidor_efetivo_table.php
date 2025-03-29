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
        Schema::table('servidor_efetivo', function (Blueprint $table) {
            $table->dropUnique(['matricula']);
            $table->index('matricula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servidor_efetivo', function (Blueprint $table) {
            $table->unique('matricula');
        });
    }
};
