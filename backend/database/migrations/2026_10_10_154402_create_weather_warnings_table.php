<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_warnings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('level')->index();
            $table->string('type');
            $table->string('district');
            $table->string('status')->default('处置中');
            $table->timestamp('published_at')->index();
            $table->string('responsible_unit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_warnings');
    }
};
