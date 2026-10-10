<?php

namespace App\Filament\Widgets;

use App\Models\Device;
use App\Models\WeatherWarning;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonitoringOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [Stat::make('监测设备', Device::count())->description('已建档设备')->color('primary'), Stat::make('在线设备', Device::where('status', 'online')->count())->description('演示状态，待接设备心跳')->color('success'), Stat::make('有效预警', WeatherWarning::where('status', '!=', '已完成')->count())->description('正在处置的预警')->color('danger')];
    }
}
