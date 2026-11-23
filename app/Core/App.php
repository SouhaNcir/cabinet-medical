<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public function run(): void
    {
        $url = $_GET['url'] ?? 'auth/login';

        if ($url === 'health') {
            $controller = new \App\Controllers\HealthController();
            $controller->index();
            return;
        }

        $segments = array_values(array_filter(explode('/', trim((string) $url, '/'))));

        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'AuthController';
        $method = $segments[1] ?? 'login';
        $params = array_slice($segments, 2);

        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            $controllerClass = 'App\\Controllers\\ErrorController';
            $method = 'notFound';
            $params = [];
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            $controller = new \App\Controllers\ErrorController();
            $method = 'notFound';
            $params = [];
        }

        call_user_func_array([$controller, $method], $params);
    }
}
