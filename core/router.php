<?php

class Router
{

    public function run()
    {

        $route = $_GET['route'] ?? 'home/index';

        $route = trim($route, '/');

        $route = filter_var($route, FILTER_SANITIZE_URL);

        $parts = explode('/', $route);

        $controllerName = ucfirst($parts[0] ?? 'Home') . "Controller";

        $methodName = $parts[1] ?? 'index';

        $params = array_slice($parts, 2);

        $controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {

            http_response_code(404);
            echo "404 Controller tidak ditemukan";
            exit;
        }

        require_once $controllerFile;

        $controller = new $controllerName;

        if (!method_exists($controller, $methodName)) {

            http_response_code(404);
            echo "404 Method tidak ditemukan";
            exit;
        }

        call_user_func_array([$controller, $methodName], $params);
    }

}