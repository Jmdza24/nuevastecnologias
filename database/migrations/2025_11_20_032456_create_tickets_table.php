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

            $table->string('subject'); // asunto
            $table->text('description'); // descripción

            $table->enum('status', [
                'open',
                'in_progress',
                'waiting_client',
                'finished',
                'closed'
            ])->default('open');

            // Relación con usuario cliente que generó el ticket
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');

            // Relación con técnico asignado (nullable)
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Fecha de cierre del ticket
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
