<?php
# Class Session ini berfungsi mengatur session baik untuk aktor admin maupun user
# =================================================================================

class Session
{
    /**
     * Menyimpan data ke dalam session.
     *
     * @param string $key   Nama key session
     * @param mixed  $value Nilai yang akan disimpan ke session
     */
    public static function set($key, $value)
    {
        // Memastikan session sudah aktif sebelum digunakan
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Menyimpan nilai ke variabel session
        $_SESSION[$key] = $value;
    }

    /**
     * Mengambil data dari session berdasarkan key.
     *
     * @param string $key Nama key session
     * @return mixed|null Mengembalikan nilai session atau null jika tidak ada
     */
    public static function get($key)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[$key] ?? null;
    }

    #buat bersihin session + cookie ketika logout
    public static function destroy()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    #buat mencegah halaman user tidak di-cache
    public static function preventCache()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    public static function checkUserLogin()
    {
        if (!self::get('user_id')) {
            header('Location: /pblperpustakaan');
            exit;
        }
    }

    public static function checkAdminLogin(){
        if (!self::get('admin_id')) {
            header('Location: /pblperpustakaan');
            exit;
        }
    }
}
