<?php

class Session
{

    private static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            session_start();
        }
    }

    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    public static function setOld($data)
    {
        self::start();
        $_SESSION['_old_input'] = $data;
    }

    public static function getOld()
    {
        self::start();
        $data = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        return $data;
    }
    public static function flash($key)
    {
        self::start();

        if(!isset($_SESSION[$key])) {
            return null;
        }

        $value = $_SESSION[$key];
        unset($_SESSION[$key]);

        return $value;
    }

    public static function regenerate()
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function destroy()
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $p = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }

        session_destroy();
    }

    public static function preventCache()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    public static function checkUserLogin()
    {
        if (!self::get('user_id')) {
            header('Location: ' . app_config()['base_url']);
            exit;
        }
    }

    public static function checkAdminLogin()
    {
        if (!self::get('admin_id')) {
            header('Location: ' . app_config()['base_url']);
            exit;
        }
    }
}