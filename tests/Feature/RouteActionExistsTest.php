<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteActionExistsTest extends TestCase
{
    /**
     * Test that all registered controller routes point to methods that actually exist.
     *
     * @return void
     */
    public function test_all_controller_route_actions_exist()
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
            } elseif (is_array($controllerAction) && count($controllerAction) === 2) {
                [$controller, $method] = $controllerAction;
                $controllerClass = is_object($controller) ? get_class($controller) : $controller;

                if (!class_exists($controllerClass)) {
                    $missingActions[] = [
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'controller' => $controllerClass,
                        'method' => $method,
                        'reason' => 'Controller class does not exist',
                    ];
                    continue;
                }

                if (!method_exists($controllerClass, $method)) {
                    $missingActions[] = [
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'controller' => $controllerClass,
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
