<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_calls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->foreignId('cabinet_id')
                ->nullable()
                ->constrained('cabinets')
                ->nullOnDelete();

            $table->foreignId('called_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->integer('numero_appel')->default(1);

            $table->timestamp('called_at')->nullable();

            $table->string('message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_calls');
    }
};