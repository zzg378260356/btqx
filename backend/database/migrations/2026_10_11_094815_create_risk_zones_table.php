<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('risk_zones', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('level')->default('blue')->index();
            $table->string('district');
            $table->decimal('area_sq_km', 12, 4)->default(0);
            $table->unsignedInteger('threatened_people')->default(0);
            $table->decimal('threatened_property', 14, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_zones');
    }
};
