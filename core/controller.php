<?php
# CORE: CONTROLLER
# Parent class untuk semua controller.
# Berisi helper:
# - model()     → memuat model
# - view()      → memuat file view
# - redirect()  → mengarahkan halaman
class Controller
{
    # Memuat model (otomatis mencari di app/models)
    public function model(string $model)
    {
        $path = __DIR__ . "/../app/models/{$model}.php";

        if (!file_exists($path)) {
            throw new Exception("Model {$model} tidak ditemukan.");
        }

        require_once $path;
        return new $model;
    }

    # Memuat view dengan data
    public function view(string $view, array $data = [])
    {
        $file = __DIR__ . "/../app/views/{$view}.php";

        if (!file_exists($file)) {
            throw new Exception("View {$view} tidak ditemukan.");
        }

        extract($data);
        require $file;
    }

    # Redirect ke URL lain
    public function redirect(string $path)
    {
        $base = app_config()['base_url'];
        header("Location: {$base}{$path}");
        exit;
    }
}
