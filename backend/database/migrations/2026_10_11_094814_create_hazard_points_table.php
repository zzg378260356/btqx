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
        Schema::create('hazard_points', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('hazard_type');
            $table->string('level')->default('medium');
            $table->string('district');
            $table->text('description')->nullable();
            $table->unsignedInteger('threatened_people')->default(0);
            $table->decimal('threatened_property', 14, 2)->default(0);
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamp('discovered_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazard_points');
    }
};
