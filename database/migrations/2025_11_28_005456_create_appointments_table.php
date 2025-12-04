<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // usuario que hizo la cita
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // mascota
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();

            // slot de fecha/hora
            $table->foreignId('appointment_slot_id')->constrained()->cascadeOnDelete();

            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('reason')->nullable();

            $table->timestamps();

            // misma mascota no puede ocupar el mismo slot dos veces
            $table->unique(['pet_id', 'appointment_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
