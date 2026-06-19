<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')
                ->nullable()
                ->constrained('patients')
                ->nullOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('cabinet_id')
                ->nullable()
                ->constrained('cabinets')
                ->nullOnDelete();

            $table->string('numero_ticket'); // EX: A001, PED-001
            $table->date('date_ticket');

            $table->enum('statut', [
                'EN_ATTENTE',
                'APPELE',
                'EN_CONSULTATION',
                'TERMINE',
                'ABSENT',
                'ANNULE'
            ])->default('EN_ATTENTE');

            $table->integer('priorite')->default(0); // 0 normal, 1 urgent

            $table->string('author')->nullable();
            $table->unsignedBigInteger('refUser')->nullable();
            $table->enum('deleted', ['NON', 'OUI'])->default('NON');

            $table->timestamps();

            $table->unique(['numero_ticket', 'date_ticket']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};