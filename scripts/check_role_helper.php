<?php
require __DIR__ . '/../vendor/autoload.php';
$perms = \App\Helpers\RoleHelper::getRolePermissions('Developer');
print_r($perms);
echo "has dashboard? ";
echo \App\Helpers\RoleHelper::hasPermission('Developer','dashboard') ? 'Y' : 'N';

// Check DGM academic access
$g = 'DGM';
echo PHP_EOL . "DGM permissions:\n";
print_r(\App\Helpers\RoleHelper::getRolePermissions($g));
echo "DGM canAccessAcademicManagement? ";
echo \App\Helpers\RoleHelper::canAccessAcademicManagement($g) ? 'Y' : 'N';
