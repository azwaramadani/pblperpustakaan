<?php
# ===============================================================
# CORE: SESSION
# ===============================================================
# Mengatur sistem login user & admin.
# Berisi helper:
# - loginUser()
# - loginAdmin()
# - logout()
# - checkUser()
# - checkAdmin()
# ===============================================================

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    # Login User
    public static function loginUser($data)
    {
        self::start();
        $_SESSION['user'] = $data;
    }

    # Login Admin
    public static function loginAdmin($data)
    {
        self::start();
        $_SESSION['admin'] = $data;
    }

    # Logout semua
    public static function logout()
    {
        self::start();
        session_destroy();
    }

    # Cek apakah user login
    public static function checkUser()
    {
        self::start();
        return isset($_SESSION['user']);
    }

    # Cek apakah admin login
    public static function checkAdmin()
    {
        self::start();
        return isset($_SESSION['admin']);
    }

    # Ambil data user
    public static function user()
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    # Ambil data admin
    public static function admin()
    {
        self::start();
        return $_SESSION['admin'] ?? null;
    }
}
