<?php
/*
KONFIGURASI GLOBAL APLIKASI RUDY
 - File ini berfungsi sebagai pusat pengaturan aplikasi.
 - Semua bagian MVC (controller, model, helper, dsb.)
 - membaca nilai konfigurasi melalui fungsi app_config().
 */

if (!function_exists('app_config')) {
    function app_config()
    {
        return [

            # ============================================
            # IDENTITAS APLIKASI
            # ============================================
            'app_name'     => 'RUDY - Ruang Study',
            'app_tagline'  => 'Sistem Peminjaman Ruangan Perpustakaan',
            'version'      => '1.0.0',

            # ============================================
            # GLOBAL SETTINGS
            # ============================================
            'base_url'     => (
                                isset($_SERVER['HTTP_X_FORWARDED_PROTO']) 
                                    ? $_SERVER['HTTP_X_FORWARDED_PROTO'] 
                                    : (
                                        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                                            ? 'https'
                                            : 'http'
                                    )
                            ) . '://' . $_SERVER['HTTP_HOST'] . '/pblperpustakaan',
            'timezone'     => 'Asia/Jakarta',

            # ============================================
            # DATABASE (untuk PDO)
            # ============================================
            'database' => [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'pblperpustakaan',
                'charset'  => 'utf8mb4'
            ],

            # ============================================
            # UPLOAD PATHS
            # ============================================
            'upload_paths' => [
                'bukti_aktivasi'   => __DIR__ . '/../storage/uploads/bukti_aktivasi/',
                'surat_peminjaman' => __DIR__ . '/../storage/uploads/surat_peminjaman_ruangrapat/',
            ],

            # ============================================
            # SECURITY & SESSION
            # ============================================
            'session_lifetime' => 3600, # 1 jam
            'cancel_limit_per_day' => 2,

            # ============================================
            # INFO DEVELOPER
            # ============================================
            'developer' => [
                'maintainer'  => 'Kelompok 4, RUDY Developers',
                'github_repo' => 'https://github.com/azwaramadani/pblperpustakaan',
            ],
        ];
    }
}

# Set timezone otomatis
date_default_timezone_set(app_config()['timezone']);
