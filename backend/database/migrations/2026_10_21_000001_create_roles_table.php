<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->json('permissions');
            $table->timestamps();
        });

        $now = now();
        DB::table('roles')->insert([
            ['slug' => 'super_admin', 'name' => '超级管理员', 'permissions' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'operator', 'name' => '业务操作员', 'permissions' => json_encode(['devices.view', 'devices.create', 'devices.update', 'devices.delete', 'hazards.view', 'hazards.create', 'hazards.update', 'hazards.delete', 'risks.view', 'risks.create', 'risks.update', 'risks.delete', 'warnings.view', 'warnings.create', 'warnings.update', 'warnings.delete', 'contacts.view', 'contacts.create', 'contacts.update', 'contacts.delete']), 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'viewer', 'name' => '只读查看员', 'permissions' => json_encode(['devices.view', 'hazards.view', 'risks.view', 'warnings.view', 'contacts.view']), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
