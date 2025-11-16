<?php
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    # =========================================================
    # HALAMAN LOGIN USER
    # URL: ?route=Auth/login
    # =========================================================
    public function login()
    {
        require __DIR__ . '/../views/auth/login_user.php';
    }

    # =========================================================
    # PROSES LOGIN USER
    # URL: ?route=Auth/loginProcess
    # =========================================================
    public function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?route=Auth/login");
            exit;
        }

        # Sanitasi input
        $nim_nip  = trim($_POST['nim_nip'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $userModel = new User();
        $user = $userModel->findByNIMNIP($nim_nip);

        # =========================================================
        # VALIDASI 1: Akun tidak ditemukan
        # =========================================================
        if (!$user) {
            Session::set("flash_error", "Akun tidak ditemukan.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # =========================================================
        # VALIDASI 2: Akun diblokir
        # =========================================================
        if ($user['status_akun'] === 'Diblokir') {
            Session::set("flash_error", "Akun anda diblokir oleh sistem.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # =========================================================
        # VALIDASI 3: Password salah
        # =========================================================
        if (!password_verify($password, $user['password'])) {
            Session::set("flash_error", "Password salah.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # =========================================================
        # VALIDASI 4: Status belum disetujui admin
        # =========================================================
        if ($user['status_akun'] !== 'Disetujui') {
            Session::set("flash_error", "Akun belum divalidasi admin. Status: " . $user['status_akun']);
            header("Location: ?route=Auth/login");
            exit;
        }

        # =========================================================
        # LOGIN SUKSES → SET SESSION
        # =========================================================
        Session::set("user_id", $user['user_id']);
        Session::set("nama", $user['nama']);
        Session::set("nim_nip", $user['nim_nip']);

        # =========================================================
        # JIKA ADA LOGIN ADMIN → SISIPKAN DI SINI (opsional)
        # =========================================================
        // if ($nim_nip === 'admin123') {
        //     header("Location: ?route=Admin/dashboard");
        //     exit;
        // }

        # =========================================================
        # Redirect user ke halaman home
        # =========================================================
        header("Location: ?route=User/home");
        exit;
    }
}
