<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\HazardInstallationPlan;
use App\Models\HazardPoint;
use App\Models\PlannedDeviceInstallation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_returns_imported_points_with_multiple_devices_and_excludes_demo_devices(): void
    {
        Device::factory()->count(2)->create();
        $point = HazardPoint::factory()->create(['code' => 'SG38', 'name' => '测试监测点', 'hazard_type' => '泥石流', 'district' => '石拐区', 'longitude' => 110.2, 'latitude' => 40.7]);
        $plan = HazardInstallationPlan::create(['hazard_point_id' => $point->id, 'source_document' => '安装表.xlsx', 'source_row' => 2, 'point_name' => $point->name, 'hazard_type' => '泥石流', 'longitude' => 110.2, 'latitude' => 40.7]);
        foreach (['gnss', 'camera'] as $type) {
            $device = Device::factory()->create(['type' => $type, 'longitude' => 110.2, 'latitude' => 40.7]);
            PlannedDeviceInstallation::create(['hazard_installation_plan_id' => $plan->id, 'device_id' => $device->id, 'device_type' => $type, 'sequence' => 1, 'longitude' => 110.2, 'latitude' => 40.7]);
        }
        config(['services.tianditu.key' => 'test-map-key']);
        $this->getJson('/api/monitoring-map')->assertOk()->assertJsonPath('summary.points', 1)->assertJsonPath('summary.devices', 2)->assertJsonPath('summary.online', 2)->assertJsonCount(2, 'points.0.devices')->assertJsonPath('points.0.code', 'SG38')->assertJsonPath('center.longitude', 109.8403)->assertJsonPath('tianditu_key', 'test-map-key');
    }
}
