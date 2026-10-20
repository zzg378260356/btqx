<?php

return [
    'roles' => [
        'super_admin' => ['devices.view', 'devices.manage', 'warnings.view', 'warnings.manage', 'users.manage', 'system.manage'],
        'operator' => ['devices.view', 'devices.manage', 'warnings.view', 'warnings.manage'],
        'viewer' => ['devices.view', 'warnings.view'],
    ],
];
