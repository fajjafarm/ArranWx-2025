<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiCacheTable extends Migration
{
    public function up()
    {
        Schema::create('api_cache', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Unique key for cache (e.g., "weather_{lat}_{lon}")
            $table->json('data'); // JSON response from API
            $table->timestamp('expires_at'); // Cache expiration time
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_cache');
    }
}