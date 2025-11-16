<?php
class Session
{
    public static function set($key, $value)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[$key] ?? null;
    }

    public static function destroy()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }

    public static function checkUserLogin()
    {
        if (!self::get('user_id')) {
            header('Location: /login');
            exit;
        }
    }
}
