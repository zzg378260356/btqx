<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\HazardPoint;
use App\Models\PlannedDeviceInstallation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class ImportInstallationDevicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_is_idempotent_and_all_device_types_share_the_point_coordinates(): void
    {
        $existing = Device::factory()->create();
        $file = $this->workbook();
        try {
            $this->artisan('btqx:import-installation-devices', ['path' => $file, '--dry-run' => true])->assertSuccessful();
            $this->assertDatabaseCount('hazard_points', 0);
            $this->artisan('btqx:import-installation-devices', ['path' => $file])->assertSuccessful();
            $this->artisan('btqx:import-installation-devices', ['path' => $file])->assertSuccessful();
            $this->assertDatabaseCount('hazard_points', 2);
            $this->assertDatabaseCount('planned_device_installations', 5);
            $this->assertDatabaseCount('devices', 6);
            $this->assertDatabaseHas('devices', ['id' => $existing->id]);
            $this->assertDatabaseHas('devices', ['code' => 'INSTALL-SG38-gnss-2', 'type' => '普通GNSS', 'longitude' => 110.199444, 'latitude' => 40.748944, 'status' => 'offline']);
            $this->assertDatabaseHas('devices', ['code' => 'INSTALL-SG38-camera-1', 'longitude' => 110.199444, 'latitude' => 40.748944]);
            $this->assertDatabaseHas('hazard_points', ['code' => 'INSTALL-003']);
            $this->assertEquals(3, PlannedDeviceInstallation::whereHas('installationPlan', fn ($query) => $query->where('source_code', 'SG38'))->count());
            $this->assertEquals('需确认', HazardPoint::where('code', 'SG38')->first()->description);
        } finally {
            unlink($file);
        }
    }

    public function test_invalid_coordinates_abort_before_any_writes(): void
    {
        $file = $this->workbook('200');
        try {
            $this->artisan('btqx:import-installation-devices', ['path' => $file])->assertFailed();
            $this->assertDatabaseCount('hazard_points', 0);
            $this->assertDatabaseCount('devices', 0);
        } finally {
            unlink($file);
        }
    }

    private function workbook(string $longitude = '110.199444'): string
    {
        $file = tempnam(sys_get_temp_dir(), 'installation-test-');
        $zip = new ZipArchive;
        $zip->open($file, ZipArchive::OVERWRITE);
        $rows = [
            ['A' => '1', 'B' => 'SG38', 'C' => '测试泥石流', 'D' => '泥石流', 'E' => $longitude, 'F' => '40.748944', 'H' => '2', 'L' => '1', 'N' => '需确认'],
            ['A' => '2', 'B' => '—', 'C' => '测试滑坡', 'D' => '滑坡', 'E' => '110.258278', 'F' => '40.658389', 'I' => '1', 'J' => '1'],
        ];
        $body = '';
        foreach ($rows as $index => $row) {
            $number = $index + 2;
            $body .= '<row r="'.$number.'">';
            foreach ($row as $column => $value) {
                $body .= '<c r="'.$column.$number.'" t="inlineStr"><is><t>'.htmlspecialchars($value, ENT_XML1).'</t></is></c>';
            }
            $body .= '</row>';
        }
        $zip->addFromString('xl/worksheets/sheet1.xml', '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$body.'</sheetData></worksheet>');
        $zip->close();

        return $file;
    }
}
