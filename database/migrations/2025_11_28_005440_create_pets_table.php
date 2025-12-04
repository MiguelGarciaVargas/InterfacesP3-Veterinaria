<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();

            // dueño de la mascota (tabla users)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // tipo de animal (tabla animal_types)
            $table->foreignId('animal_type_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birth_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
