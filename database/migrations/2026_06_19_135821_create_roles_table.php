<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            $table->string('nom')->unique();
            $table->string('description')->nullable();

            $table->enum('statut', ['ACTIF', 'INACTIF'])->default('ACTIF');

            $table->string('author')->nullable();
            $table->unsignedBigInteger('refUser')->nullable();
            $table->enum('deleted', ['NON', 'OUI'])->default('NON');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};