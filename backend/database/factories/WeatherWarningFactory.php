<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WeatherWarningFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => '东河区暴雨风险预警', 'level' => 'red', 'type' => '暴雨', 'district' => '东河区', 'status' => '处置中', 'published_at' => now(), 'responsible_unit' => '气象局'];
    }
}
