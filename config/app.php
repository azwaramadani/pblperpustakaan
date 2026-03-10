<?php

if (!function_exists('app_config')) {
    function app_config()
    {
        return [

            'app_name' => 'RUDY - Ruang Study',
            'app_tagline' => 'Sistem Peminjaman Ruangan Perpustakaan',
            'version' => '1.0.0',

            # BASE URL (lebih stabil untuk production)
            'base_url' => 'http://localhost/pblperpustakaan',

            'timezone' => 'Asia/Jakarta',

            # DATABASE
            'database' => [
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname' => 'pblperpustakaan',
                'charset' => 'utf8mb4'
            ],

            # UPLOAD
            'upload_paths' => [
                'bukti_aktivasi' => __DIR__ . '/../storage/uploads/bukti_aktivasi/',
                'surat_peminjaman' => __DIR__ . '/../storage/uploads/surat_peminjaman_ruangrapat/',
            ],

            # SESSION
            'session_lifetime' => 3600,

            # SYSTEM
            'cancel_limit_per_day' => 2,

            'developer' => [
                'maintainer' => 'Kelompok 4, RUDY Developers',
                'github_repo' => 'https://github.com/azwaramadani/pblperpustakaan',
            ],

        ];
    }
}

date_default_timezone_set(app_config()['timezone']);