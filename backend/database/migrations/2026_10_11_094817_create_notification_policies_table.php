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
        Schema::create('notification_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('level')->index();
            $table->json('channels');
            $table->json('recipient_scope')->nullable();
            $table->unsignedInteger('retry_interval_seconds')->default(300);
            $table->unsignedTinyInteger('max_retries')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_policies');
    }
};
