<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('nom');
            $table->string('postnom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('sexe', 10)->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();

            $table->string('numero_patient')->nullable()->unique();

            $table->string('author')->nullable();
            $table->unsignedBigInteger('refUser')->nullable();
            $table->enum('deleted', ['NON', 'OUI'])->default('NON');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};