<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();        // Dog, Cat, etc
            $table->string('image_url')->nullable(); // URL random de imagen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_types');
    }
};
