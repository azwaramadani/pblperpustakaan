<?php
# ===============================================================
# CORE: ROUTER
# ===============================================================
# Router utama aplikasi RUDY.
# Mengarahkan:
#     domain.com/?route=controller/method/param1/param2
# ===============================================================

class Router
{
    public function run()
    {
        # Ambil route dari URL
        $route = $_GET['route'] ?? 'home/index';
        $route = trim($route, '/');

        $parts = explode('/', $route);

        # Tentukan controller & method
        $controllerName = ucfirst($parts[0] ?? 'Home') . "Controller";
        $methodName     = $parts[1] ?? 'index';

        $params = array_slice($parts, 2);

        # Path file controller
        $controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo "Controller {$controllerName} tidak ditemukan.";
            exit;
        }

        require_once $controllerFile;

        $controller = new $controllerName;

        if (!method_exists($controller, $methodName)) {
            http_response_code(404);
            echo "Method {$methodName} tidak ditemukan.";
            exit;
        }

        # Jalankan method dengan parameter
        call_user_func_array([$controller, $methodName], $params);
    }
}
