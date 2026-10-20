<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hazard_installation_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hazard_point_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_document');
            $table->unsignedSmallInteger('source_row');
            $table->string('source_code')->nullable()->index();
            $table->string('point_name');
            $table->string('hazard_type');
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->text('layout_notes')->nullable();
            $table->text('field_notes')->nullable();
            $table->string('verification_status')->default('pending')->index();
            $table->timestamps();

            $table->unique(['source_document', 'source_row']);
        });

        Schema::create('planned_device_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hazard_installation_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('device_type');
            $table->unsignedSmallInteger('sequence');
            $table->string('installation_position')->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->string('status')->default('planned')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['hazard_installation_plan_id', 'device_type', 'sequence'], 'planned_device_type_sequence_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planned_device_installations');
        Schema::dropIfExists('hazard_installation_plans');
    }
};
