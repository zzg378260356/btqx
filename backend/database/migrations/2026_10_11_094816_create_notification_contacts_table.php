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
        Schema::create('notification_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('district')->nullable()->index();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->json('notification_channels')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_contacts');
    }
};
