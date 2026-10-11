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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warning_id')->nullable()->constrained('weather_warnings')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('notification_contacts')->nullOnDelete();
            $table->string('channel');
            $table->string('status')->index();
            $table->string('provider_message_id')->nullable();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
