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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom_service');
            $table->string('code_service')->nullable(); // EX: PED, GYN, GEN
            $table->string('description')->nullable();
            $table->enum('statut', ['ACTIF', 'INACTIF'])->default('ACTIF');

            $table->string('author')->nullable();
            $table->unsignedBigInteger('refUser')->nullable();
            $table->enum('deleted', ['NON', 'OUI'])->default('NON');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
