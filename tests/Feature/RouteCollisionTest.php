<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteCollisionTest extends TestCase
{
    /**
     * Test that there are no duplicate route names in the compiled application routes.
     */
    public function test_no_duplicate_route_names()
    {
        $routes = Route::getRoutes()->getRoutes();
        $names = [];
        $duplicates = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name) {
                if (isset($names[$name])) {
                    $duplicates[$name][] = [
                        'methods' => implode('|', $route->methods()),
                        'uri' => $route->uri(),
                        'action' => $route->getActionName(),
                    ];
                    $duplicates[$name][] = $names[$name];
                } else {
                    $names[$name] = [
                        'methods' => implode('|', $route->methods()),
                        'uri' => $route->uri(),
                        'action' => $route->getActionName(),
                    ];
                }
            }
        }

        $this->assertEmpty(
            $duplicates,
            'Found duplicate route names in compiled routes: ' . json_encode($duplicates, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that there are no duplicate Method + URI definitions in routes/web.php.
     */
    public function test_no_duplicate_method_and_uri_in_web_routes()
    {
        $tokens = token_get_all(file_get_contents(base_path('routes/web.php')));
        $prefixStack = [];
        $braceDepth = 0;
        $prefixDepths = [];
        $declaredRoutes = [];
        $duplicates = [];

        $total = count($tokens);
        for ($i = 0; $i < $total; $i++) {
            $token = $tokens[$i];

            // Track braces for prefix scoping
            if (is_string($token)) {
                if ($token === '{') {
                    $braceDepth++;
                } elseif ($token === '}') {
                    while (!empty($prefixDepths) && end($prefixDepths) === $braceDepth) {
                        array_pop($prefixDepths);
                        array_pop($prefixStack);
                    }
                    $braceDepth--;
                }
                continue;
            }

            // Track Route::prefix('...')
            if ($token[0] === T_STRING && strtolower($token[1]) === 'prefix') {
                // Look ahead for string argument
                for ($j = $i + 1; $j < min($i + 10, $total); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_CONSTANT_ENCAPSED_STRING) {
                        $prefixVal = trim($tokens[$j][1], "'\"");
                        $prefixStack[] = $prefixVal;
                        $prefixDepths[] = $braceDepth + 1;
                        break;
                    }
                }
            }

            // Track Route::get/post/put/delete/patch
            if ($token[0] === T_STRING && in_array(strtolower($token[1]), ['get', 'post', 'put', 'delete', 'patch'])) {
                // Check if preceded by Route:: or ->
                $methodName = strtoupper($token[1]);
                $routeUri = null;

                for ($j = $i + 1; $j < min($i + 10, $total); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_CONSTANT_ENCAPSED_STRING) {
                        $routeUri = trim($tokens[$j][1], "'\"");
                        break;
                    }
                    if (is_string($tokens[$j]) && $tokens[$j] === ';') {
                        break;
                    }
                }

                if ($routeUri !== null) {
                    $combinedPrefix = implode('/', array_filter($prefixStack));
                    $fullUri = trim($combinedPrefix . '/' . ltrim($routeUri, '/'), '/');
                    $key = "$methodName /$fullUri";

                    if (isset($declaredRoutes[$key])) {
                        $duplicates[] = [
                            'route' => $key,
                            'line' => $token[2],
                        ];
                    } else {
                        $declaredRoutes[$key] = $token[2];
                    }
                }
            }
        }

        $this->assertEmpty(
            $duplicates,
            'Found duplicate Method + URI definitions in routes/web.php: ' . json_encode($duplicates, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that all registered controller routes point to valid controller methods.
     */
    public function test_all_route_controller_actions_exist()
    {
        $routes = Route::getRoutes()->getRoutes();
        $missingActions = [];

        foreach ($routes as $route) {
            $action = $route->getAction();

            if (!isset($action['controller'])) {
                continue;
            }

            $controllerAction = $action['controller'];

            if (is_string($controllerAction) && str_contains($controllerAction, '@')) {
                [$controller, $method] = explode('@', $controllerAction);

                if (!class_exists($controller)) {
                    $missingActions[] = [
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'controller' => $controller,
                        'method' => $method,
                        'reason' => 'Controller class does not exist',
                    ];
                    continue;
                }

                if (!method_exists($controller, $method)) {
                    $missingActions[] = [
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'controller' => $controller,
                        'method' => $method,
                        'reason' => 'Method does not exist on controller',
                    ];
                }
            }
        }

        $this->assertEmpty(
            $missingActions,
            'Found routes pointing to non-existent controller methods: ' . json_encode($missingActions, JSON_PRETTY_PRINT)
        );
    }
}
