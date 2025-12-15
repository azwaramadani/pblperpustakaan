<?php
/**
 * ============================================================================
 * CORE: Router
 * ============================================================================
 * Class Router merupakan router utama aplikasi RUDY.
 *
 * Router ini bertugas untuk:
 * - Membaca parameter `route` dari URL
 * - Menentukan controller, method, dan parameter
 * - Memanggil controller dan method yang sesuai
 *
 * Pola URL yang digunakan:
 *     domain.com/?route=controller/method/param1/param2
 *
 * Misal:
 *     ?route=booking/detail/12
 *     → BookingController::detail(12)
 */

class Router
{
    /**
     * Menjalankan proses routing aplikasi.
     *
     * Method ini akan:
     * 1. Mengambil route dari URL
     * 2. Menentukan nama controller dan method
     * 3. Memuat file controller
     * 4. Mengeksekusi method dengan parameter (jika ada)
     */
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
