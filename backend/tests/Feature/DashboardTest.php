<?php

namespace Tests\Feature;

use App\Filament\Resources\Devices\Pages\CreateDevice;
use App\Filament\Resources\WeatherWarnings\Pages\CreateWeatherWarning;
use App\Models\Device;
use App\Models\User;
use App\Models\WeatherWarning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_reflects_devices_and_only_active_warnings(): void
    {
        Device::factory()->count(2)->create();
        Device::factory()->create(['status' => 'offline']);
        WeatherWarning::factory()->create(['level' => 'red']);
        WeatherWarning::factory()->create(['status' => '已完成']);
        $this->getJson('/api/dashboard')->assertOk()->assertJsonPath('devices.total', 3)->assertJsonPath('devices.online', 2)->assertJsonPath('counts.red', 1)->assertJsonCount(1, 'warnings')->assertJsonCount(7, 'trend.dates');
    }

    public function test_admin_requires_login_and_rejects_non_admin(): void
    {
        $this->get('/admin/devices')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/devices')->assertForbidden();
    }

    public function test_admin_can_create_a_device_and_validation_rejects_invalid_coordinates(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();
        $this->actingAs($user);
        Livewire::test(CreateDevice::class)->fillForm(['name' => '测试设备', 'code' => 'BT-TEST', 'type' => '雨量计', 'district' => '东河区', 'latitude' => 40.65, 'longitude' => 109.83, 'status' => 'online'])->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('devices', ['code' => 'BT-TEST']);
        Livewire::test(CreateDevice::class)->fillForm(['name' => '错误设备', 'code' => 'BT-BAD', 'type' => '雨量计', 'district' => '东河区', 'latitude' => 100, 'longitude' => 109.83, 'status' => 'online'])->call('create')->assertHasFormErrors(['latitude']);
    }

    public function test_admin_warning_creation_appears_in_dashboard(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();
        $this->actingAs($user);
        Livewire::test(CreateWeatherWarning::class)->fillForm(['title' => '测试暴雨预警', 'level' => 'orange', 'type' => '暴雨', 'district' => '东河区', 'status' => '处置中', 'published_at' => now()->format('Y-m-d H:i:s'), 'responsible_unit' => '气象局'])->call('create')->assertHasNoFormErrors();
        $this->getJson('/api/dashboard')->assertJsonPath('counts.orange', 1)->assertJsonPath('warnings.0.title', '测试暴雨预警');
    }
}
