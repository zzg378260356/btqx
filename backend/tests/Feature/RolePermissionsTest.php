<?php

namespace Tests\Feature;

use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permissions_control_menu_and_each_operation(): void
    {
        Role::create(['slug' => 'inspector', 'name' => '巡检员', 'permissions' => ['devices.view', 'devices.update']]);
        $user = User::factory()->create(['role' => 'inspector']);
        $this->actingAs($user);

        $this->assertTrue(DeviceResource::canViewAny());
        $this->assertFalse(DeviceResource::canCreate());
        $this->assertTrue(DeviceResource::canEdit(null));
        $this->assertFalse(DeviceResource::canDelete(null));
        $this->assertFalse(DeviceResource::canDeleteAny());

        Role::where('slug', 'inspector')->update(['permissions' => ['devices.create']]);
        $user->unsetRelation('assignedRole');

        $this->assertFalse(DeviceResource::canViewAny());
        $this->assertTrue(DeviceResource::canCreate());
        $this->assertFalse(DeviceResource::canEdit(null));
    }

    public function test_unassigned_role_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'missing']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_super_admin_can_open_role_management(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)->get('/admin/roles')->assertOk();
        $this->actingAs($user)->get('/admin/roles/create')->assertOk();
    }

    public function test_role_form_saves_selected_child_permissions(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'super_admin']));

        Livewire::test(CreateRole::class)
            ->fillForm(['name' => '设备巡检', 'slug' => 'device_inspector', 'permissions' => ['devices.view', 'devices.update']])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(['devices.view', 'devices.update'], Role::where('slug', 'device_inspector')->firstOrFail()->permissions);
    }
}
