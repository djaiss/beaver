<?php

declare(strict_types=1);
use Illuminate\Routing\Router;

test('web routes use the allowed controller action names', function () {
    $allowedMethods = [
        'index',
        'new',
        'create',
        'show',
        'edit',
        'update',
        'destroy',
    ];

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

        expect($allowedMethods)->toContain($method);
        expect($route->getName())->not->toBeNull();
        expect($route->getName())->toEndWith('.'.$method);
    }
});
