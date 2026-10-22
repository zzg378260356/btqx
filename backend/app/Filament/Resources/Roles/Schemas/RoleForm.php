<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $groups = [];
        foreach (config('permissions.modules') as $module => $label) {
            $children = [];
            foreach (config('permissions.actions') as $action => $actionLabel) {
                if (in_array($module, ['users', 'roles'], true) && $action !== 'view') {
                    continue;
                }

                $children["{$module}.{$action}"] = $actionLabel;
            }

            $groups[] = ['label' => $label, 'children' => $children];
        }

        return $schema->components([
            TextInput::make('name')->label('角色名称')->required()->maxLength(100),
            TextInput::make('slug')->label('角色标识')->required()->alphaDash()->unique(ignoreRecord: true)->disabledOn('edit')->maxLength(100),
            ViewField::make('permissions')->label('菜单与操作权限')->default([])->view('filament.forms.components.permission-tree')->viewData(['groups' => $groups]),
        ]);
    }
}
