<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\HazardInstallationPlan;
use App\Models\HazardPoint;
use App\Models\PlannedDeviceInstallation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use ZipArchive;

#[Signature('btqx:import-installation-devices {path : 安装表xlsx路径} {--dry-run : 只校验并统计，不写入数据库}')]
#[Description('按安装表逐行导入隐患监测点和设备；同点设备共用主坐标，支持重复执行')]
class ImportInstallationDevices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $rows = $this->readRows((string) $this->argument('path'));
            $types = ['G' => ['weather_station', '自动气象站'], 'H' => ['gnss', '普通GNSS'], 'I' => ['beidou_gnss', '北斗GNSS'], 'J' => ['rain_gauge', '雨量计'], 'K' => ['flow_meter', '流量计'], 'L' => ['camera', '摄像头']];
            $totals = array_fill_keys(array_column($types, 1), 0);
            foreach ($rows as $row) {
                foreach ($types as $column => [, $name]) {
                    $totals[$name] += (int) ($row[$column] ?? 0);
                }
            }
            $this->info('监测点 '.count($rows).' 处，设备 '.array_sum($totals).' 台（以逐行数量为准）。');
            $this->table(['类型', '数量'], collect($totals)->map(fn (int $count, string $name): array => [$name, $count])->values()->all());
            if ($this->option('dry-run')) {
                return self::SUCCESS;
            }
            $source = basename((string) $this->argument('path'));
            DB::transaction(function () use ($rows, $types, $source): void {
                foreach ($rows as $row) {
                    $sourceCode = trim($row['B'] ?? '');
                    $code = $sourceCode !== '' && $sourceCode !== '—' ? $sourceCode : 'INSTALL-'.str_pad((string) $row['_row'], 3, '0', STR_PAD_LEFT);
                    $point = HazardPoint::firstOrNew(['code' => $code]);
                    $point->fill(['name' => $row['C'], 'hazard_type' => $row['D'], 'longitude' => $row['E'], 'latitude' => $row['F'], 'district' => '石拐区', 'description' => trim(($row['M'] ?? '')."\n".($row['N'] ?? ''))]);
                    $point->save();
                    $plan = HazardInstallationPlan::updateOrCreate(['source_document' => $source, 'source_row' => $row['_row']], [
                        'hazard_point_id' => $point->id, 'source_code' => $sourceCode, 'point_name' => $row['C'], 'hazard_type' => $row['D'],
                        'longitude' => $row['E'], 'latitude' => $row['F'], 'layout_notes' => $row['M'] ?? null, 'field_notes' => $row['N'] ?? null,
                    ]);
                    foreach ($types as $column => [$type, $name]) {
                        for ($sequence = 1; $sequence <= (int) ($row[$column] ?? 0); $sequence++) {
                            $installation = PlannedDeviceInstallation::firstOrNew(['hazard_installation_plan_id' => $plan->id, 'device_type' => $type, 'sequence' => $sequence]);
                            $device = $installation->device_id ? Device::findOrFail($installation->device_id) : Device::firstOrNew(['code' => 'INSTALL-'.$code.'-'.$type.'-'.$sequence]);
                            $device->fill(['name' => $row['C'].'-'.$name.'-'.$sequence, 'type' => $name, 'district' => '石拐区', 'longitude' => $row['E'], 'latitude' => $row['F']]);
                            if (! $device->exists) {
                                $device->status = 'offline';
                            }
                            $device->save();
                            $installation->fill(['device_id' => $device->id, 'longitude' => $row['E'], 'latitude' => $row['F'], 'installation_position' => '暂用隐患点主坐标', 'notes' => trim(($row['M'] ?? '')."\n".($row['N'] ?? ''))]);
                            $installation->save();
                        }
                    }
                }
            });
            $this->info('导入完成。设备未接入前保持离线，重复执行不会新增重复设备。');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    /** @return list<array<string, string|int>> */
    private function readRows(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('无法打开安装表：'.$path);
        }
        try {
            $strings = [];
            $shared = $zip->getFromName('xl/sharedStrings.xml');
            if ($shared !== false) {
                $xml = simplexml_load_string($shared, \SimpleXMLElement::class, LIBXML_NONET);
                if ($xml === false) {
                    throw new RuntimeException('安装表共享字符串格式不正确。');
                }
                $xml->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($xml->xpath('//s:si') as $item) {
                    $item->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $strings[] = implode('', array_map(fn ($text): string => (string) $text, $item->xpath('.//s:t')));
                }
            }
            $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
            $xml = $sheet === false ? false : simplexml_load_string($sheet, \SimpleXMLElement::class, LIBXML_NONET);
            if ($xml === false) {
                throw new RuntimeException('安装表缺少有效的第一个工作表。');
            }
            $xml->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $rows = [];
            foreach ($xml->xpath('//s:sheetData/s:row') as $element) {
                $row = ['_row' => (int) $element['r']];
                foreach ($element->c as $cell) {
                    $column = preg_replace('/\d+/', '', (string) $cell['r']);
                    $value = (string) $cell->v;
                    $row[$column] = (string) $cell['t'] === 's' ? ($strings[(int) $value] ?? '') : ((string) $cell['t'] === 'inlineStr' ? (string) $cell->is->t : $value);
                }
                if (! ctype_digit((string) ($row['A'] ?? '')) || ! isset($row['C'])) {
                    continue;
                }
                if (! is_numeric($row['E'] ?? '') || ! is_numeric($row['F'] ?? '') || abs((float) $row['E']) > 180 || abs((float) $row['F']) > 90) {
                    throw new RuntimeException('第'.$row['_row'].'行经纬度无效，未写入任何数据。');
                }
                foreach (str_split('GHIJKL') as $column) {
                    $value = (string) ($row[$column] ?? '0');
                    if (! ctype_digit($value) || (int) $value > 1000) {
                        throw new RuntimeException('第'.$row['_row'].'行设备数量无效。');
                    }
                }
                $rows[] = $row;
            }
            if ($rows === []) {
                throw new RuntimeException('安装表没有有效的监测点记录。');
            }

            return $rows;
        } finally {
            $zip->close();
        }
    }
}
