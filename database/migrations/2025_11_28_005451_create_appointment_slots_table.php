<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_slots', function (Blueprint $table) {
            $table->id();

            // fecha y hora disponibles para cita
            $table->dateTime('starts_at');

            // por si luego quieres permitir más de 1 cita en la misma hora
            $table->unsignedInteger('capacity')->default(1);
            $table->unsignedInteger('booked')->default(0);

            $table->boolean('is_active')->default(true);

            // admin que creó este slot
            $table->foreignId('created_by')->constrained('users');

            $table->timestamps();

            // un registro por fecha/hora
            $table->unique(['starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};
