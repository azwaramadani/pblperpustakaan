<?php
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../core/Session.php';

class AuthController
{
    // handler buat redirect ke halaman login
    public function login()
    {
        $flash = $this->getFlashMessages();
        require __DIR__ . '/../views/auth/login_user.php';
    }

    // handler forgot password untuk redirect ke page yang berisi input email sesuai yang didaftarkan
    public function forgotPassword()
    {
        $flash = $this->getFlashMessages();

        require __DIR__ . '/../views/auth/forgot_password.php';
    }

    // handler button submit setelah isi email, untuk kirim link reset ke email user
    public function sendResetLink()
    {
        $email = trim($_POST['email'] ?? '');

        $userModel = new User();
        $user      = $userModel->findByEmail($email);

        // validasi email harus terdaftar di database
        if (!$user) {
            Session::set('flash_error', 'Email tidak ditemukan.');
            header('Location: ?route=Auth/forgotPassword');
            exit;
        }

        $token = bin2hex(random_bytes(32)); // isi token
        $expired = date('Y-m-d H:i:s', strtotime('+30 minutes')); // batas link hanya berlaku sampai 30 menit dari link dikirim

        // buat tokennya
        $resetModel = new PasswordReset();
        $resetModel->createToken($user['user_id'], $token, $expired);

        $resetLink = app_config()['base_url'] . "?route=Auth/resetPassword&token=$token";

        //isi email
        $body = "
            <h3>Reset Password</h3>
            <p>Klik link berikut untuk reset password:</p>
            <a href='$resetLink'>$resetLink</a>
            <p>Link ini berlaku selama 30 menit.</p>
        ";

        //kirim email dengan sendEmail() dengan parameter: email user, subject emailnya, dan isi email
        sendMail($user['email'], "Reset Password", $body);

        Session::set("flash_success", "Link reset password telah dikirim ke email.");
        header("Location: ?route=Auth/login");
    }

    // handler redirect ke page update password 
    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        $resetModel = new PasswordReset();
        $reset = $resetModel->findValidToken($token);

        // validasi token
        if (!$reset) {
            die("Token tidak valid atau sudah expired.");
        }

        $flash = $this->getFlashMessages();

        require __DIR__ . '/../views/auth/reset_password.php';
    }

    // handler
    public function updatePassword()
    {
        $token = $_POST['token'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if ($password !== $confirm) {
            Session::set('flash_error', 'Password tidak sama.');
            header('Location: ?route=Auth/resetPassword');
            exit;
        }

        $resetModel = new PasswordReset();
        $reset = $resetModel->findValidToken($token);

        if (!$reset) {
            Session::set('flash_error', 'Token tidak valid atau sudah expired.');
            header('Location: ?route=Auth/resetPassword');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $userModel = new User();
        $userModel->updatePassword($reset['user_id'], $hash);

        $resetModel->markUsed($reset['id']);

        Session::set("flash_success", "Password berhasil diubah.");
        header("Location: ?route=Auth/login");
    }
    
    // handler logout
    public function logout()
    {
        Session::destroy();
        header("Location: ?route=Auth/login");
        exit;
    }

    // handler proses login semua user
    public function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?route=Auth/login");
            exit;
        }

        #sanitasi input
        $nim_nip  = trim($_POST['username'] ?? '');
        $usernameAdmin = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $userModel = new User();
        $user = $userModel->findByNIMNIP($nim_nip);

        $adminModel = new Admin();
        $admin = $adminModel->loginAdmin($usernameAdmin); 

        # VALIDASI LOGIN ADMIN
        if ($admin) {
            if (!password_verify($password, $admin['password'])) {
                Session::set("flash_error", "Password salah.");
                header("Location: ?route=Auth/login");
                exit;
            }
            Session::set("admin_id", $admin['admin_id']);
            Session::set("username", $admin['username']);
            header("Location: ?route=Admin/dashboard");
            exit;
        }

        if ($user['deleted_at']  !== null){
            Session::set("flash_error", "Akun sudah dihapus, segera hubungi admin!");
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI 1: Akun tidak ditemukan
        if (!$user) {
            Session::set("flash_error", "Akun tidak ditemukan, silahkan daftar/register jika belum memiliki akun");
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI 2: Akun diblokir
        if ($user['status_akun'] === 'Diblokir') {
            Session::set("flash_error", "Akun anda diblokir, karena telah membatalkan booking 3x dalam 1 hari.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI 3: Status ditolak admin redirect ke page khusus edit data register 
        if ($user['status_akun'] == 'Ditolak') {
            if (!password_verify($password, $user['password'])){
                Session::set("flash_error", "username atau password salah");
                header("Location: ?route=Auth/login");
                exit;
            }

            Session::set("user_id", $user['user_id']);
            Session::set("nama", $user['nama']);

            Session::regenerate();

            header("Location: ?route=Auth/fixRegistration");
            exit;   
        }

        # VALIDASI 4: Status masih menunggu belum divalidasi admin
        if ($user['status_akun'] == 'Menunggu') {
            Session::set("flash_error", " Mohon menunggu, akun anda sedang divalidasi oleh admin. Status: " . $user['status_akun']);
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI Password user salah
        if (!password_verify($password, $user['password'])) {
            Session::set("flash_error", "username atau  password salah.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # LOGIN SUKSES → SET SESSION
        Session::set("user_id", $user['user_id']);
        Session::set("nama", $user['nama']);
        Session::set("nim_nip", $user['nim_nip']);
        Session::set("role", $user['role'] ?? '');
        Session::set("no_hp", $user['no_hp'] ?? '');
        Session::set("email", $user['email'] ?? '');
        Session::set("jurusan", $user['jurusan'] ?? '');
        Session::set("program_studi", $user['program_studi'] ?? '');

        Session::regenerate();
        
        # Redirect user ke halaman home
        header("Location: ?route=User/home");
        exit;
    }

    // handler untuk redirect user mahasiswa yang statusnya ditolak oleh admin
    public function fixRegistration() {
        $user_id = Session::get('user_id');

        if(!$user_id){
            Session::set('flash_error', 'username atau password salah.');
            header("Location: ?route=Auth/login");
            exit;
        }

        $userModel = new user();
        $user      = $userModel->findById($user_id);

        if($user['status_akun'] !== 'Ditolak'){
            header("Location: ?route=User/home");
            exit;
        }

        $flash = $this->getFlashMessages();

        require_once __DIR__ . '/../views/auth/fix_registration.php';
    }

    // handler buat upload ulang bukti aktivasi kubaca setelah ditolak admin untuk user mahasiswa
    public function submitFixRegistration()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?route=Auth/login");
            exit;
        }

        $user_id = Session::get('user_id');

        if (!$user_id) {
            Session::set('flash_error', 'username atau password salah.');
            header("Location: ?route=Auth/login");
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($user_id);

        // pastikan hanya status Ditolak
        if ($user['status_akun'] !== 'Ditolak') {
            header("Location: ?route=User/home");
            exit;
        }

        // VALIDASI FILE WAJIB ADA
        if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== 0) {
            Session::set("flash_error", "File bukti wajib diupload.");
            header("Location: ?route=Auth/fixRegistration");
            exit;
        }

        // UPLOAD FILE
        $uploadDir = __DIR__ . "/../../storage/uploads/bukti_aktivasi/";
        $fileName = uploadFile($_FILES['bukti'], $uploadDir);

        if (!$fileName) {
            Session::set("flash_error", "Upload gagal. Format harus jpg/jpeg/png/pdf dan < 5MB.");
            header("Location: ?route=Auth/fixRegistration");
            exit;
        }

        $pathDB = "storage/uploads/bukti_aktivasi/" . $fileName;

        // hapus file lama 
        if (!empty($user['bukti_aktivasi'])) {
            $oldPath = __DIR__ . "/../../" . $user['bukti_aktivasi'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // UPDATE DATA
        $userModel->updateAfterReject($user_id, $pathDB);

        // LOGOUT (karena status jadi Menunggu)
        session_destroy();

        Session::set("flash_success", "Data berhasil diperbarui, silahkan tunggu validasi admin.");
        header("Location: ?route=Auth/login");
        exit;
    }

    // handler buat redirect ke page register pilih role
    public function registerRole()
    {
        require __DIR__ . '/../views/auth/register_pilihrole.php';
    }

    // handler buat user di redirect ke masing-masing halaman yang dipilih (sesuai role)
    public function chooseRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?route=Auth/registerRole");
            exit;
        }

        $role = $_POST['role'] ?? '';

        if ($role === 'mahasiswa') {
            header("Location: ?route=Auth/registerMahasiswa");
            exit;
        }

        if ($role === 'dosen') {
            header("Location: ?route=Auth/registerDosen");
            exit;
        }

        if ($role === 'tenaga kependidikan') {
            header("Location: ?route=Auth/registerTendik");
            exit;
        }

        Session::set('flash_error', 'Silakan pilih role terlebih dahulu.');
        header("Location: ?route=Auth/registerRole");
        exit;
    }

    // handler page register mahasiswa
    public function registerMahasiswa()
    {
        $jurusanList = $this->jurusanOptions();
        $prodiList   = $this->prodiOptions();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $old = [
                'nim_nip'        => trim($_POST['nim_nip'] ?? ''),
                'jurusan'        => trim($_POST['jurusan'] ?? ''),
                'program_studi'  => trim($_POST['program_studi'] ?? ''),
                'nama'           => trim($_POST['nama'] ?? ''),
                'no_hp'          => trim($_POST['no_hp'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
            ];

            $password        = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // ================= VALIDASI =================

            // CAPTCHA
            if (($$_POST['captcha_input'] ?? '') !== ($_SESSION['captcha_code'] ?? '')) {
                Session::set('flash_error', 'Captcha salah atau tidak sesuai.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // REQUIRED FIELD
            foreach ($old as $key => $value) {
                if ($value === '') {
                    Session::set('flash_error', 'Semua kolom wajib diisi.');
                    Session::setOld($old);
                    header("Location: ?route=Auth/registerMahasiswa");
                    exit;
                }
            }

            if ($password === '' || $confirmPassword === '') {
                Session::set('flash_error', 'Password wajib diisi.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // EMAIL FORMAT
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                Session::set('flash_error', 'Format email tidak valid.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // PASSWORD MATCH
            if ($password !== $confirmPassword) {
                Session::set('flash_error', 'Konfirmasi password tidak sesuai.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // PASSWORD LENGTH
            if (strlen($password) < 8) {
                Session::set('flash_error', 'Password minimal 8 karakter.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            $userModel = new User();

            // NIM DUPLICATE
            if ($userModel->isNIMExists($old['nim_nip'])) {
                Session::set('flash_error', 'NIM/NIP sudah terdaftar.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // EMAIL DUPLICATE
            if ($userModel->isEmailExists($old['email'])) {
                Session::set('flash_error', 'Email sudah terdaftar.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // UPLOAD FILE
            $uploadName = uploadFile($_FILES['bukti_aktivasi'] ?? null, app_config()['upload_paths']['bukti_aktivasi']);

            if (!$uploadName) {
                Session::set('flash_error', 'Upload bukti aktivasi gagal.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerMahasiswa");
                exit;
            }

            // ================= SUCCESS =================

            $userModel->registerMahasiswa([
                'nim_nip'         => $old['nim_nip'],
                'jurusan'         => $old['jurusan'],
                'program_studi'   => $old['program_studi'],
                'nama'            => $old['nama'],
                'no_hp'           => $old['no_hp'],
                'email'           => $old['email'],
                'password'        => $password,
                'role'            => 'Mahasiswa',
                'bukti_aktivasi'  => 'storage/uploads/bukti_aktivasi/' . $uploadName
            ]);

            Session::set('flash_success', 'Berhasil Membuat Akun! Mohon menunggu validasi admin.');
            header("Location: ?route=Auth/registerMahasiswa");
            exit;
        }

        // ================= GET REQUEST =================

        $flash = $this->getFlashMessages();
        $old   = Session::getOld() ?? [
            'nim_nip'        => '',
            'jurusan'        => '',
            'program_studi'  => '',
            'nama'           => '',
            'no_hp'          => '',
            'email'          => ''
        ];

        require __DIR__ . '/../views/auth/register_usermahasiswa.php';
    }

    // handler page register dosen
    public function registerDosen()
    {
        $jurusanList = $this->jurusanOptions();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $old = [
                'nim_nip'        => trim($_POST['nim_nip'] ?? ''),
                'jurusan'        => trim($_POST['jurusan'] ?? ''),
                'nama'           => trim($_POST['nama'] ?? ''),
                'no_hp'          => trim($_POST['no_hp'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
            ];

            $password        = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // ================= VALIDASI =================
            // CAPTCHA
            if (($$_POST['captcha_input'] ?? '') !== ($_SESSION['captcha_code'] ?? '')) {
                Session::set('flash_error', 'Captcha salah atau tidak sesuai.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            // REQUIRED FIELD
            foreach ($old as $key => $value) {
                if ($value === '') {
                    Session::set('flash_error', 'Semua kolom wajib diisi.');
                    Session::setOld($old);
                    header("Location: ?route=Auth/registerDosen");
                    exit;
                }
            }

            if ($password === '' || $confirmPassword === '') {
                Session::set('flash_error', 'Password wajib diisi.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            // EMAIL FORMAT
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                Session::set('flash_error', 'Format email tidak valid.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            // PASSWORD MATCH
            if ($password !== $confirmPassword) {
                Session::set('flash_error', 'Konfirmasi password tidak sesuai.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            // PASSWORD LENGTH
            if (strlen($password) < 8) {
                Session::set('flash_error', 'Password minimal 8 karakter.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            $userModel = new User();

            // NIM DUPLICATE
            if ($userModel->isNIMExists($old['nim_nip'])) {
                Session::set('flash_error', 'NIP sudah terdaftar.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            // EMAIL DUPLICATE
            if ($userModel->isEmailExists($old['email'])) {
                Session::set('flash_error', 'Email sudah terdaftar.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerDosen");
                exit;
            }

            // ================= SUCCESS =================
            $userModel->registerDosen([
                'nim_nip'         => $old['nim_nip'],
                'jurusan'         => $old['jurusan'],
                'nama'            => $old['nama'],
                'no_hp'           => $old['no_hp'],
                'email'           => $old['email'],
                'password'        => $password,
                'role'            => 'Dosen',
            ]);

            Session::set('flash_success', 'Berhasil Membuat Akun! Silahkan Login.');
            header("Location: ?route=Auth/registerDosen");
            exit;
        }

        // ================= GET REQUEST =================
        $flash = $this->getFlashMessages();
        $old   = Session::getOld() ?? [
            'nim_nip'        => '',
            'jurusan'        => '',
            'nama'           => '',
            'no_hp'          => '',
            'email'          => ''
        ];

        require __DIR__ . '/../views/auth/register_userdosen.php';
    }

    // handler page register tenaga kependidikan 
    public function registerTendik()
    {
        $unitList = $this->unitOptions();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $old = [
                'nim_nip'        => trim($_POST['nim_nip'] ?? ''),
                'unit'           => trim($_POST['unit'] ?? ''),
                'nama'           => trim($_POST['nama'] ?? ''),
                'no_hp'          => trim($_POST['no_hp'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
            ];

            $password        = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // ================= VALIDASI =================
            // CAPTCHA
            if (($$_POST['captcha_input'] ?? '') !== ($_SESSION['captcha_code'] ?? '')) {
                Session::set('flash_error', 'Captcha salah atau tidak sesuai.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            // REQUIRED FIELD
            foreach ($old as $key => $value) {
                if ($value === '') {
                    Session::set('flash_error', 'Semua kolom wajib diisi.');
                    Session::setOld($old);
                    header("Location: ?route=Auth/registerTendik");
                    exit;
                }
            }

            if ($password === '' || $confirmPassword === '') {
                Session::set('flash_error', 'Password wajib diisi.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            // EMAIL FORMAT
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                Session::set('flash_error', 'Format email tidak valid.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            // PASSWORD MATCH
            if ($password !== $confirmPassword) {
                Session::set('flash_error', 'Konfirmasi password tidak sesuai.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            // PASSWORD LENGTH
            if (strlen($password) < 8) {
                Session::set('flash_error', 'Password minimal 8 karakter.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            $userModel = new User();

            // NIM DUPLICATE
            if ($userModel->isNIMExists($old['nim_nip'])) {
                Session::set('flash_error', 'NIP sudah terdaftar.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            // EMAIL DUPLICATE
            if ($userModel->isEmailExists($old['email'])) {
                Session::set('flash_error', 'Email sudah terdaftar.');
                Session::setOld($old);
                header("Location: ?route=Auth/registerTendik");
                exit;
            }

            // ================= SUCCESS =================
            $userModel->registerTendik([
                'nim_nip'         => $old['nim_nip'],
                'unit'            => $old['unit'],  
                'nama'            => $old['nama'],
                'no_hp'           => $old['no_hp'],
                'email'           => $old['email'],
                'password'        => $password,
                'role'            => 'Tenaga Kependidikan',
            ]);

            Session::set('flash_success', 'Berhasil Membuat Akun! Silahkan Login.');
            header("Location: ?route=Auth/registerTendik");
            exit;
        }

        // ================= GET REQUEST =================
        $flash = $this->getFlashMessages();
        $old   = Session::getOld() ?? [
            'nim_nip'        => '',
            'jurusan'        => '',
            'nama'           => '',
            'no_hp'          => '',
            'email'          => ''
        ];

        require __DIR__ . '/../views/auth/register_usertendik.php';
    }

    private function jurusanOptions(): array
    {
        return [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Akuntansi',
            'Administrasi Niaga'
        ];
    }

    private function unitOptions() : array
    {
        return[
            'Perpustakaan',
            'Teknologi Informasi dan Komunikasi',
            'Rekayasa Teknologi dan Produk Unggulan',
            'Perawatan dan Perbaikan',
            'Pengembangan Karier dan Kewirausahaan',
            'Layanan Uji Kompetensi'
        ];
    }

    private function prodiOptions(): array
    {
        return [
                'Konstruksi Sipil',
                'Konstruksi Gedung',
                'Teknik Perancangan Jalan dan Jembatan',
                'Teknik Konstruksi Gedung',
                'Teknik Mesin',
                'Teknik Konversi Energi',
                'Alat Berat',
                'Manufaktur',
                'Teknologi Rekayasa Manufaktur (d.h. Manufaktur)',
                'Pembangkit Tenaga Listrik',
                'Teknologi Rekayasa Pembangkit Energi (d.h. Pembangkit Tenaga Listrik)',
                'Teknologi Rekayasa Konversi Energi',
                'Teknologi Rekayasa Pemeliharaan Alat Berat',
                'Elektronika Industri',
                'Teknik Listrik',
                'Telekomunikasi',
                'Instrumentasi Kontrol Industri',
                'Teknik Otomasi Listrik Industri',
                'Broadband Multimedia',
                'Akuntansi',
                'Keuangan dan Perbankan',
                'Akuntansi Keuangan',
                'Keuangan dan Perbankan Syariah',
                'Manajemen Keuangan',
                'Manajemen Pemasaran (WNBK)',
                'Administrasi Bisnis',
                'Administrasi Bisnis Terapan',
                'Usaha Jasa Konvensi, Perjalanan Insentif dan Pameran /MICE',
                'Bahasa Inggris untuk Komunikasi Bisnis dan Profesional',
                'Penerbitan',
                'Teknik Grafika',
                'Desain Grafis',
                'Teknologi Industri Cetak Kemasan',
                'Teknologi Rekayasa Cetak Dan Grafis 3 Dimensi',
                'Teknik Informatika',
                'Teknik Multimedia Digital',
                'Teknik Multimedia dan Jaringan',
                'Teknik Komputer dan Jaringan',
                'Magister Rekayasa Teknologi Manufaktur',
                'Magister Teknik Elektro'
        ];
    }

    private function getFlashMessages(){
        return [
            'success'   => Session::flash('flash_success'),
            'error'     => Session::flash('flash_error')
        ];
    }
}