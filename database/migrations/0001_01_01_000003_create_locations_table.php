<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('latitude');
            $table->float('longitude');
            $table->float('altitude'); // Altitude in meters
            $table->enum('type', ['Village', 'Marine', 'Hill'])->default('Village'); // Updated to enum
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
}