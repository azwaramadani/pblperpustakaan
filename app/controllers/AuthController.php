<?php
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../core/Session.php';


class AuthController
{
    #method handler buat redirect ke page register pilih role
    public function registerRole()
    {
        $error = Session::get('flash_error');
        Session::set('flash_error', null);
        require __DIR__ . '/../views/auth/register_pilihrole.php';
    }

    #method handler buat user di redirect ke masing-masing halaman yang dipilih (sesuai role)
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

        Session::set('flash_error', 'Silakan pilih role terlebih dahulu.');
        header("Location: ?route=Auth/registerRole");
        exit;
    }

    public function registerMahasiswa()
    {
        $errors = [];
        $success = Session::get('flash_success');
        Session::set('flash_success', null);
        $jurusanList = $this->jurusanOptions();
        $prodiList = $this->prodiOptions();

        $old = [
            'nim_nip' => '',
            'jurusan' => '',
            'program_studi' => '',
            'nama'    => '',
            'no_hp'   => '',
            'email'   => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nim_nip'] = trim($_POST['nim_nip'] ?? '');
            $old['jurusan'] = trim($_POST['jurusan'] ?? '');
            $old['program_studi'] = trim($_POST['program_studi'] ?? '');
            $old['nama']    = trim($_POST['nama'] ?? '');
            $old['no_hp']   = trim($_POST['no_hp'] ?? '');
            $old['email']   = trim($_POST['email'] ?? '');
            $password       = $_POST['password'] ?? '';
            $confirmPassword= $_POST['confirm_password'] ?? '';

            if ($old['nim_nip'] === '' || $old['jurusan'] === '' || $old['program_studi'] === '' || $old['nama'] === '' || $old['no_hp'] === '' || $old['email'] === '' || $password === '' || $confirmPassword === '') {
                $errors[] = 'Semua kolom wajib diisi.';
            }

            if ($old['email'] && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Konfirmasi password tidak sesuai.';
            }

            $userModel = new User();

            if ($userModel->isNIMExists($old['nim_nip'])) {
                $errors[] = 'NIM/NIP sudah terdaftar.';
            }

            if ($userModel->isEmailExists($old['email'])) {
                $errors[] = 'Email sudah terdaftar.';
            }

            if (empty($errors)) {
                $uploadName = uploadFile($_FILES['bukti_aktivasi'] ?? null, app_config()['upload_paths']['bukti_aktivasi']);

                if (!$uploadName) {
                    $errors[] = 'Upload bukti aktivasi gagal. Pastikan file gambar dipilih.';
                } else {
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

                    Session::set('flash_success', 'Berhasil Membuat Akun!, Mohon Menunggu Validasi Admin.');
                    header("Location: ?route=Auth/registerMahasiswa");
                    exit;
                }
            }
        }

        require __DIR__ . '/../views/auth/register_usermahasiswa.php';
    }

    public function registerDosen()
    {
        $errors = [];
        $success = Session::get('flash_success');
        Session::set('flash_success', null);
        $jurusanList = $this->jurusanOptions();

        $old = [
            'nim_nip' => '',
            'jurusan' => '',
            'nama'    => '',
            'no_hp'   => '',
            'email'   => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['nim_nip'] = trim($_POST['nim_nip'] ?? '');
            $old['jurusan'] = trim($_POST['jurusan'] ?? '');
            $old['nama']    = trim($_POST['nama'] ?? '');
            $old['no_hp']   = trim($_POST['no_hp'] ?? '');
            $old['email']   = trim($_POST['email'] ?? '');
            $password       = $_POST['password'] ?? '';
            $confirmPassword= $_POST['confirm_password'] ?? '';

            if ($old['nim_nip'] === '' || $old['jurusan'] === '' || $old['nama'] === '' || $old['no_hp'] === '' || $old['email'] === '' || $password === '' || $confirmPassword === '') {
                $errors[] = 'Semua kolom wajib diisi.';
            }

            if ($old['email'] && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid.';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Konfirmasi password tidak sesuai.';
            }

            $userModel = new User();

            if ($userModel->isNIMExists($old['nim_nip'])) {
                $errors[] = 'NIM/NIP sudah terdaftar.';
            }

            if ($userModel->isEmailExists($old['email'])) {
                $errors[] = 'Email sudah terdaftar.';
            }

            if (empty($errors)) {
                $userModel->registerDosen([
                    'nim_nip'  => $old['nim_nip'],
                    'jurusan'  => $old['jurusan'],
                    'nama'     => $old['nama'],
                    'no_hp'    => $old['no_hp'],
                    'email'    => $old['email'],
                    'password' => $password,
                    'role'     => 'Dosen'
                ]);

                Session::set('flash_success', 'Berhasil Membuat Akun!');
                header("Location: ?route=Auth/registerDosen");
                exit;
            }
        }

        require __DIR__ . '/../views/auth/register_userdosentendik.php';
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
    

    #method buat redirect ke halaman login
    public function login()
    {
        require __DIR__ . '/../views/auth/login_user.php';
    }

    # BUAT LOGOUT
    public function logout()
    {
        Session::destroy();
        header("Location: ?route=Auth/login");
        exit;
    }

    # PROSES LOGIN USER
    public function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?route=Auth/login");
            exit;
        }

        #sanitasi input
        $nim_nip  = trim($_POST['nim_nip'] ?? '');
        $username = trim($_POST['nim_nip'] ?? '');
        $password = trim($_POST['password'] ?? '');

        #bikin objek dari masing2 class buat nentuin yang login admin apa user
        $userModel = new User();
        $user = $userModel->findByNIMNIP($nim_nip);
        $adminModel = new Admin();
        $admin = $adminModel->loginAdmin($username); 

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

        # VALIDASI 1: Akun tidak ditemukan
        if (!$user) {
            Session::set("flash_error", "Akun tidak ditemukan.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI 2: Akun diblokir
        if ($user['status_akun'] === 'Diblokir') {
            Session::set("flash_error", "Akun anda diblokir, karena telah membatalkan booking 3x dalam 1 hari.");
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI 4: Status masih menunggu belum divalidasi admin
        if ($user['status_akun'] == 'Menunggu') {
            Session::set("flash_error", " Mohon menunggu, akun anda sedang divalidasi oleh admin. Status: " . $user['status_akun']);
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI 4: Status belum ditolak admin
        if ($user['status_akun'] == 'Ditolak') {
            Session::set("flash_error", " Registrasi akun anda ditolak karena tidak melampirkan bukti aktivasi Kubaca dengan benar, segera hubungi admin. Status: " . $user['status_akun']);
            header("Location: ?route=Auth/login");
            exit;
        }

        # VALIDASI Password user salah
        if (!password_verify($password, $user['password'])) {
            Session::set("flash_error", "Password salah.");
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
        
        # Redirect user ke halaman home
        header("Location: ?route=User/home");
        exit;
    }
}
