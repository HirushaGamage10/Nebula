<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\RoleHelper;

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║     COMPREHENSIVE ROLEHELPER ROUTE CONFIGURATION CHECK      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Get all unique permissions from RoleHelper
$allRoleHelperPermissions = [];
foreach (RoleHelper::PERMISSIONS as $role => $permissions) {
    foreach ($permissions as $permission) {
        $allRoleHelperPermissions[$permission] = true;
    }
}

echo "✓ Total unique route permissions defined in RoleHelper: " . count($allRoleHelperPermissions) . "\n\n";

// Parse web.php to extract all named routes
$webPhpContent = file_get_contents(__DIR__ . '/../routes/web.php');
preg_match_all("/->name\('([^']+)'\)/", $webPhpContent, $matches);
$namedRoutesInWebPhp = array_unique($matches[1]);

echo "✓ Total named routes found in web.php: " . count($namedRoutesInWebPhp) . "\n\n";

// Routes that are protected but not in RoleHelper
$protectedRoutes = [];
preg_match_all("/Route::middleware\(\['[^']*role:([^]]+)\]\)/", $webPhpContent, $roleMatches);

echo "════════════════════════════════════════════════════════════════\n";
echo "CRITICAL ISSUES: Routes in web.php NOT configured in RoleHelper\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$missingRoutes = [];
foreach ($namedRoutesInWebPhp as $route) {
    // Skip auth-related routes that don't need role permissions
    if (in_array($route, ['login', 'login.authenticate', 'logout'])) {
        continue;
    }
    
    if (!isset($allRoleHelperPermissions[$route])) {
        $missingRoutes[] = $route;
    }
}

if (count($missingRoutes) > 0) {
    echo "⚠️  WARNING: " . count($missingRoutes) . " routes found in web.php but NOT in RoleHelper:\n\n";
    foreach ($missingRoutes as $route) {
        echo "   ❌ $route\n";
    }
    echo "\n⚡ ACTION REQUIRED: Add these routes to appropriate roles in RoleHelper.php\n\n";
} else {
    echo "✅ All routes in web.php are properly configured in RoleHelper!\n\n";
}

echo "════════════════════════════════════════════════════════════════\n";
echo "UNUSED PERMISSIONS: In RoleHelper but not found in web.php\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$unusedPermissions = [];
foreach (array_keys($allRoleHelperPermissions) as $permission) {
    if (!in_array($permission, $namedRoutesInWebPhp)) {
        $unusedPermissions[] = $permission;
    }
}

if (count($unusedPermissions) > 0) {
    echo "ℹ️  INFO: " . count($unusedPermissions) . " permissions in RoleHelper not matching web.php routes:\n\n";
    foreach ($unusedPermissions as $permission) {
        echo "   ⚠️  $permission\n";
    }
    echo "\n💡 NOTE: These may be valid (used in middleware, renamed routes, or legacy).\n";
    echo "   Review each to ensure they're intentional.\n\n";
} else {
    echo "✅ All RoleHelper permissions match routes in web.php!\n\n";
}

echo "════════════════════════════════════════════════════════════════\n";
echo "ROLE-SPECIFIC PERMISSION ANALYSIS\n";
echo "════════════════════════════════════════════════════════════════\n\n";

foreach (RoleHelper::ROLES as $roleKey => $roleName) {
    $permissions = RoleHelper::PERMISSIONS[$roleKey] ?? [];
    $validCount = 0;
    $invalidCount = 0;
    
    foreach ($permissions as $permission) {
        if (in_array($permission, $namedRoutesInWebPhp) || 
            in_array($permission, ['login', 'logout', 'dashboard', 'user.profile'])) {
            $validCount++;
        } else {
            $invalidCount++;
        }
    }
    
    $status = $invalidCount > 0 ? "⚠️ " : "✅";
    echo sprintf("%s %-43s: %2d permissions (%d valid, %d questionable)\n", 
        $status, $roleName, count($permissions), $validCount, $invalidCount);
}

echo "\n════════════════════════════════════════════════════════════════\n";
echo "DETAILED ROLE PERMISSION ISSUES\n";
echo "════════════════════════════════════════════════════════════════\n\n";

foreach (RoleHelper::ROLES as $roleKey => $roleName) {
    $permissions = RoleHelper::PERMISSIONS[$roleKey] ?? [];
    $issues = [];
    
    foreach ($permissions as $permission) {
        if (!in_array($permission, $namedRoutesInWebPhp) && 
            !in_array($permission, ['login', 'logout', 'dashboard', 'user.profile'])) {
            $issues[] = $permission;
        }
    }
    
    if (count($issues) > 0) {
        echo "🔍 $roleName:\n";
        foreach ($issues as $issue) {
            echo "   ⚠️  Permission '$issue' - route not found in web.php\n";
        }
        echo "\n";
    }
}

echo "════════════════════════════════════════════════════════════════\n";
echo "RECOMMENDATIONS\n";
echo "════════════════════════════════════════════════════════════════\n\n";

if (count($missingRoutes) > 0) {
    echo "1. ⚠️  URGENT: Add missing routes to RoleHelper permissions:\n";
    foreach (array_slice($missingRoutes, 0, 5) as $route) {
        echo "   - $route\n";
    }
    if (count($missingRoutes) > 5) {
        echo "   ... and " . (count($missingRoutes) - 5) . " more\n";
    }
    echo "\n";
}

if (count($unusedPermissions) > 3) {
    echo "2. 💡 Consider reviewing unused permissions for cleanup or verification\n\n";
}

echo "3. ✓ Ensure middleware role checks match RoleHelper permissions\n";
echo "4. ✓ Test each role's access to verify configurations\n\n";

echo "════════════════════════════════════════════════════════════════\n";
echo "ANALYSIS COMPLETE\n";
echo "════════════════════════════════════════════════════════════════\n\n";
