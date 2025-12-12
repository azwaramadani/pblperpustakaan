<?php
# Composer autoload (Buat PHPSpreadsheet (admin buat laporan) dan PHPMailer (simulasi notif akun user udah siap dipakai))
require_once __DIR__ . '/vendor/autoload.php';

# ================================================
# AUTOLOAD untuk semua class (Controller, Model, Core)
# ================================================

spl_autoload_register(function ($class) {
    # Prioritas: core
    $corePath = __DIR__ . '/core/' . $class . '.php';
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }

    # Model
    $modelPath = __DIR__ . '/app/models/' . strtolower($class) . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
        return;
    }

    # Controller
    $controllerPath = __DIR__ . '/app/controllers/' . $class . '.php';
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        return;
    }
});

# ================================================
# MUAT KONFIGURASI
# ================================================
require_once __DIR__ . '/config/app.php';
# ================================================
# MUAT HELPER GLOBAL
# ================================================
require_once __DIR__ . '/core/helper.php';
# ================================================
# JALANKAN ROUTER
# ================================================
$router = new Router();
$router->run();
