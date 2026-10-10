<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->word().'监测站', 'code' => fake()->unique()->bothify('BT-####??'), 'type' => '自动气象站', 'district' => '东河区', 'latitude' => 40.65, 'longitude' => 109.83, 'status' => 'online', 'last_seen_at' => now()];
    }
}
