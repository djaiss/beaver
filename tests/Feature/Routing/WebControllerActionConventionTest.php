<?php

declare(strict_types=1);

namespace Tests\Feature\Routing;

use Illuminate\Routing\Router;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebControllerActionConventionTest extends TestCase
{
    private const array ALLOWED_METHODS = [
        'index',
        'new',
        'create',
        'show',
        'edit',
        'update',
        'destroy',
    ];

    #[Test]
    public function web_routes_use_the_allowed_controller_action_names(): void
    {
        $routes = $this->app->make(Router::class)->getRoutes();

        foreach ($routes as $route) {
            $action = $route->getActionName();
            if (! in_array('web', $route->middleware(), true)) {
                continue;
            }
            if ($action === 'Closure') {
                continue;
            }
            if (! str_starts_with((string) $action, 'App\\Http\\Controllers\\')) {
                continue;
            }
            if (str_starts_with((string) $action, 'App\\Http\\Controllers\\App\\Auth\\')) {
                continue;
            }

            [, $method] = explode('@', (string) $action, 2);

            $this->assertContains($method, self::ALLOWED_METHODS, $action);
            $this->assertNotNull($route->getName(), $action);
            $this->assertStringEndsWith('.'.$method, $route->getName(), $action);
        }
    }
}
