<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetsTable extends Migration
{
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('species_id')->constrained('species');
            $table->foreignId('breed_id')->constrained('breeds');
            $table->date('birth_date');
            $table->decimal('weight', 5, 2)->nullable(); // peso en kg
            $table->enum('sex', ['macho', 'hembra']);
            $table->string('color')->nullable();
            $table->text('distinctive_marks')->nullable(); // señas particulares
            $table->unsignedBigInteger('owner_id'); // ID del propietario (referencia al auth-service)
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Índices
            $table->index('owner_id');
            $table->index(['species_id', 'breed_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pets');
    }
}