<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use App\Models\WeatherWarning;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        User::firstOrCreate(['email' => 'admin@btqx.local'], ['name' => 'admin', 'username' => 'admin', 'password' => fake()->password(16), 'is_admin' => true, 'role' => 'super_admin']);
        $districts = ['东河区', '昆都仑区', '青山区', '白云鄂博矿区', '达尔罕茂明安联合旗'];
        for ($i = 0; $i < 24; $i++) {
            Device::firstOrCreate(['code' => sprintf('BTQX-%04d', $i + 1)], ['name' => $districts[$i % 5].'监测站 '.($i + 1), 'type' => ['自动气象站', '雨量计', '风速仪'][$i % 3], 'district' => $districts[$i % 5], 'latitude' => 40.65 + $i * .001, 'longitude' => 109.83 + $i * .001, 'status' => $i % 7 === 0 ? 'offline' : 'online', 'last_seen_at' => now()->subMinutes($i % 7 === 0 ? 120 : 2)]);
        }
        for ($i = 0; $i < 28; $i++) {
            $type = ['暴雨', '雷电', '大风', '冰雹'][$i % 4];
            WeatherWarning::firstOrCreate(['title' => $districts[$i % 5].'-'.$type.'风险预警 '.($i + 1)], ['level' => ['red', 'orange', 'yellow', 'blue'][$i % 4], 'type' => $type, 'district' => $districts[$i % 5], 'status' => $i % 5 === 0 ? '已完成' : '处置中', 'published_at' => now()->subDays($i % 7)->subMinutes($i * 3), 'responsible_unit' => ['气象局', '应急管理局', '水务局'][$i % 3]]);
        }
    }
}
