<?php
require_once __DIR__ . '/../../core/Session.php';

class AdminController {
    #method handler buat dashboard admin
    public function dashboard()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel    = new Admin();
        $bookingModel  = new Booking();
        $feedbackModel = new Feedback();
        $userModel     = new User();
        $roomModel     = new Room();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);

        #ini buat card paling atas
        $today = date('Y-m-d');
        $stats = [
            'user_today'        => $userModel->countRegisteredToday($today),
            'user_mustvalidate' => $userModel->mustvalidateRegistered(),
            'booking_today'     => $bookingModel->countBookingToday($today),
            'room_active'       => $roomModel->countActiverooms(),
            'user_total'        => $userModel->countAllusers(), 
        ];

        #buat set auto selesai kalo jam booking udah nyentuh jam selesai
        $bookingModel->markFinishedBookings();

        #filter dashboard data booking + feedback
        $sortCreate  = strtolower($_GET['sort_create'] ?? 'desc');
        $sortDate    = strtolower($_GET['sort_date'] ?? 'desc');
        $fromDate    = $_GET['from_date'] ?? '';
        $toDate      = $_GET['to_date'] ?? '';
        $roleSel     = $_GET['role'] ?? '';
        $unitSel     = $_GET['unit'] ?? '';
        $jurusanSel  = $_GET['jurusan'] ?? '';
        $prodiSel    = $_GET['program_studi'] ?? '';
        $feedbackSel = $_GET['feedback'] ?? '';
        $keyword     = trim($_GET['keyword'] ?? '');

        
        # pagination setup
        $perPage = 10; // jumlah baris per halaman
        $pageReq = (int)($_GET['page'] ?? 1);
        $page    = $pageReq > 0 ? $pageReq : 1;

        # ambil data + total sesuai filter + halaman
        $pagination = $bookingModel->getAllSortedPaginatedToday(
                        $sortCreate,
                        $roleSel ?: null,
                        $unitSel ?: null,
                        $jurusanSel ?: null,
                        $prodiSel ?: null,
                        $keyword ?: null,
                        $perPage,
                        $page);
        
        $feedbackpagination = $feedbackModel->feedbackgetAllSortedPaginated(
                        $sortDate,
                        $roleSel ?: null,
                        $unitSel ?: null,
                        $jurusanSel ?: null,
                        $prodiSel ?: null,
                        $feedbackSel ?: null,
                        $keyword ?: null,
                        $perPage,
                        $page);
        
        $todayBookings = $pagination['data'];    
        $feedbacks     = $feedbackpagination['data'];

        $roleList     = $this->roleOptions();
        $unitList     = $this->unitOptions();
        $jurusanList  = $this->jurusanOptions();
        $prodiList    = $this->prodiOptions();
        $feedbackList = $this->feedbackOptions();

        $filters = [
            'sort_create'   => $sortCreate,
            'role'          => $roleSel,
            'unit'          => $unitSel,
            'jurusan'       => $jurusanSel,
            'program_studi' => $prodiSel,
            'keyword'       => $keyword,
        ];

        #Ruangan dengan Booking terbanyak
        $topRooms  = $bookingModel->getTopRoomsByBooking(9);

        $fbFilters = [
            'sort_date'     => $sortDate,
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'role'          => $roleSel,
            'unit'          => $unitSel,
            'jurusan'       => $jurusanSel,
            'program_studi' => $prodiSel,
            'feedback'      => $feedbackSel,
            'keyword'       => $keyword,
        ];

        $success = Session::get('flash_success');
        $error   = Session::get('flash_error');
        Session::set('flash_success', null);
        Session::set('flash_error', null);

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    //method handler data peminjaman admin
    public function dataPeminjaman()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel   = new Admin();
        $bookingModel = new Booking();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);
        
        #filter dashboard data booking
        $sortDate   = strtolower($_GET['sort_date'] ?? 'desc');
        $fromDate   = $_GET['from_date'] ?? '';
        $toDate     = $_GET['to_date'] ?? '';
        $roleSel    = $_GET['role'] ?? '';
        $unitSel    = $_GET['unit'] ?? '';
        $jurusanSel = $_GET['jurusan'] ?? '';
        $prodiSel   = $_GET['program_studi'] ?? '';
        $keyword    = trim($_GET['keyword'] ?? ''); // kata kunci nama penanggung jawab

        # pagination setup
        $perPage = 10; // jumlah baris per halaman
        $pageReq = (int)($_GET['page'] ?? 1);
        $page    = $pageReq > 0 ? $pageReq : 1;

        # ambil data + total sesuai filter + halaman
        $pagination = $bookingModel->getAllSortedPaginated(
                        $sortDate, 
                        $fromDate ?: null, 
                        $toDate ?: null,
                        $roleSel ?: null,
                        $unitSel ?: null,
                        $jurusanSel ?: null,
                        $prodiSel ?: null,
                        $keyword ?: null,
                        $perPage,
                        $page);

        $bookings  = $pagination['data'];

        $roleList    = $this->roleOptions();
        $unitList    = $this->unitOptions();
        $jurusanList = $this->jurusanOptions();
        $prodiList   = $this->prodiOptions();
        
        $filters = [
            'sort_date'     => $sortDate,
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'role'          => $roleSel,
            'unit'          => $unitSel,
            'jurusan'       => $jurusanSel,
            'program_studi' => $prodiSel,
            'keyword'       => $keyword,
        ];

        $success = Session::get('flash_success');
        $error   = Session::get('flash_error');
        Session::set('flash_success', null);
        Session::set('flash_error', null);

        require __DIR__ . '/../views/admin/data_peminjaman.php';
    }

    #method handler buat admin update status bookingan user
    public function updateStatus()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $redirect = trim($_POST['redirect'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectAfterBookingUpdate($redirect);
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $status    = trim($_POST['status_booking'] ?? '');

        $allowed = ['Disetujui','Ditolak','Dibatalkan','Selesai'];
        if (!$bookingId || !in_array($status, $allowed, true)) {
            Session::set('flash_error', 'Data tidak valid.');
            $this->redirectAfterBookingUpdate($redirect);
        }

        $bookingModel = new Booking();
        $bookingModel->updateStatus($bookingId, $status);

        Session::set('flash_success', 'Status booking berhasil diperbarui.');
        $this->redirectAfterBookingUpdate($redirect);
    }

    #method handler buat admin kelola data ruangan
    public function dataRuangan()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel = new Admin();
        $roomModel  = new Room();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);
        $rooms   = $roomModel->getAllWithStats();

        $success = Session::get('flash_success');
        $error   = Session::get('flash_error');
        Session::set('flash_success', null);
        Session::set('flash_error', null);

        require __DIR__ . '/../views/admin/data_ruangan.php';
    }

    # Form tambah ruangan
    public function addRuangan()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel = new Admin();
        $adminId    = Session::get('admin_id');
        $admin      = $adminModel->findById($adminId);

        $success = Session::get('flash_success');
        $error   = Session::get('flash_error');
        Session::set('flash_success', null);
        Session::set('flash_error', null);

        require __DIR__ . '/../views/admin/add_ruangan.php';
    }

    # Simpan ruangan baru
    public function storeRuangan()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $namaRuangan   = trim($_POST['nama_ruangan'] ?? '');
        $kapasitasMin  = (int)($_POST['kapasitas_min'] ?? 0);
        $kapasitasMax  = (int)($_POST['kapasitas_max'] ?? 0);
        $deskripsi     = trim($_POST['deskripsi'] ?? '');
        $status        = $_POST['status'] ?? 'Tersedia';
        $manualImage   = trim($_POST['gambar_ruangan_manual'] ?? '');
        $statusAllowed = ['Tersedia','Tidak Tersedia'];

        if (!in_array($status, $statusAllowed, true)) {
            $status = 'Tersedia';
        }

        if ($namaRuangan === '' || $kapasitasMin <= 0 || $kapasitasMax <= 0 || $kapasitasMin > $kapasitasMax) {
            Session::set('flash_error', 'Lengkapi data ruangan dengan benar (kapasitas min tidak boleh lebih besar dari maks).');
            header('Location: ?route=Admin/addRuangan');
            exit;
        }

        # Upload gambar kalau ada, atau pakai path manual
        $gambarPath = $this->handleRoomImageUpload($_FILES['gambar_ruangan'] ?? [], $manualImage ?: null);

        $roomModel = new Room();
        $roomModel->create([
            'gambar_ruangan' => $gambarPath,
            'nama_ruangan'   => $namaRuangan,
            'kapasitas_min'  => $kapasitasMin,
            'kapasitas_max'  => $kapasitasMax,
            'deskripsi'      => $deskripsi,
            'status'         => $status,
        ]);

        Session::set('flash_success', 'Ruangan berhasil ditambahkan.');
        header('Location: ?route=Admin/dataRuangan');
        exit;
    }

    # Form edit ruangan
    public function editRuangan($roomId)
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel = new Admin();
        $roomModel  = new Room();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);
        $room    = $roomModel->findById((int)$roomId);

        if (!$room) {
            Session::set('flash_error', 'Ruangan tidak ditemukan.');
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $success = Session::get('flash_success');
        $error   = Session::get('flash_error');
        Session::set('flash_success', null);
        Session::set('flash_error', null);

        require __DIR__ . '/../views/admin/edit_ruangan.php';
    }

    # Update data ruangan
    public function updateRuangan()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $roomId       = (int)($_POST['room_id'] ?? 0);
        $namaRuangan  = trim($_POST['nama_ruangan'] ?? '');
        $kapasitasMin = (int)($_POST['kapasitas_min'] ?? 0);
        $kapasitasMax = (int)($_POST['kapasitas_max'] ?? 0);
        $deskripsi    = trim($_POST['deskripsi'] ?? '');
        $status       = $_POST['status'] ?? 'Tersedia';
        $manualImage  = trim($_POST['gambar_ruangan_manual'] ?? '');
        $statusAllowed = ['Tersedia','Tidak Tersedia'];

        if (!in_array($status, $statusAllowed, true)) {
            $status = 'Tersedia';
        }

        $roomModel = new Room();
        $existing  = $roomModel->findById($roomId);

        if (!$existing) {
            Session::set('flash_error', 'Ruangan tidak ditemukan.');
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        if ($namaRuangan === '' || $kapasitasMin <= 0 || $kapasitasMax <= 0 || $kapasitasMin > $kapasitasMax) {
            Session::set('flash_error', 'Lengkapi data ruangan dengan benar (kapasitas min tidak boleh lebih besar dari maks).');
            header('Location: ?route=Admin/editRuangan/' . $roomId);
            exit;
        }

        $currentImage = $manualImage !== '' ? $manualImage : ($existing['gambar_ruangan'] ?? null);
        $gambarPath   = $this->handleRoomImageUpload($_FILES['gambar_ruangan'] ?? [], $currentImage);

        $roomModel->update($roomId, [
            'gambar_ruangan' => $gambarPath,
            'nama_ruangan'   => $namaRuangan,
            'kapasitas_min'  => $kapasitasMin,
            'kapasitas_max'  => $kapasitasMax,
            'deskripsi'      => $deskripsi,
            'status'         => $status,
        ]);

        Session::set('flash_success', 'Data ruangan berhasil diperbarui.');
        header('Location: ?route=Admin/dataRuangan');
        exit;
    }

    # Hapus ruangan (dengan konfirmasi modal di view)
    public function deleteRuangan()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $roomId = (int)($_POST['room_id'] ?? 0);
        if (!$roomId) {
            Session::set('flash_error', 'Data ruangan tidak valid.');
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $roomModel = new Room();
        $room      = $roomModel->findById($roomId);

        if (!$room) {
            Session::set('flash_error', 'Ruangan sudah tidak ada.');
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        # Hapus file gambar lama kalau masih tersimpan lokal
        if (!empty($room['gambar_ruangan']) && strpos($room['gambar_ruangan'], 'public/assets/image/ruangan/') === 0) {
            $oldPath = __DIR__ . '/../../' . $room['gambar_ruangan'];
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $roomModel->deleteById($roomId);

        Session::set('flash_success', 'Ruangan berhasil dihapus.');
        header('Location: ?route=Admin/dataRuangan');
        exit;
    }

     # Tampilkan feedback per ruangan
    public function feedbackRuangan($roomId)
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel    = new Admin();
        $roomModel     = new Room();
        $feedbackModel = new Feedback();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);

        $room = $roomModel->findWithStats((int)$roomId);
        if (!$room) {
            Session::set('flash_error', 'Ruangan tidak ditemukan.');
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $feedbacks = $feedbackModel->getByRoom((int)$roomId);
        $totalFeedback = count($feedbacks);
        $puasCount     = 0;
        foreach ($feedbacks as $fb) {
            if (!empty($fb['puas'])) {
                $puasCount++;
            }
        }

        $feedbackSummary = [
            'total'      => $totalFeedback,
            'puas'       => $puasCount,
            'tidak_puas' => $totalFeedback - $puasCount,
        ];

        require __DIR__ . '/../views/admin/feedback_ruangan.php';
    }

    //method handler buat admin kelola data akun user 
    public function dataAkun()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel = new Admin();
        $userModel  = new User();

        $adminId    = Session::get('admin_id');
        $admin      = $adminModel->findById($adminId);
        
        #filter data akun
        $sortDate      = strtolower($_GET['sort_date'] ?? 'desc');
        $fromDate      = $_GET['from_date'] ?? '';
        $toDate        = $_GET['to_date'] ?? '';
        $roleSel       = $_GET['role'] ?? '';
        $unitSel       = $_GET['unit'] ?? '';
        $jurusanSel    = $_GET['jurusan'] ?? '';
        $prodiSel      = $_GET['program_studi'] ?? '';
        $statusakunSel = $_GET['status_akun'] ?? '';
        $keyword       = trim($_GET['keyword'] ?? ''); // kata kunci nama penanggung jawab

        # pagination setup
        $perPage = 10; // jumlah baris per halaman
        $pageReq = (int)($_GET['page'] ?? 1);
        $page    = $pageReq > 0 ? $pageReq : 1;

        $pagination = $userModel->usergetAllSortedPaginated(
                        $sortDate, 
                        $fromDate ?: null, 
                        $toDate ?: null,
                        $roleSel ?: null,
                        $unitSel ?: null,
                        $jurusanSel ?: null,
                        $prodiSel ?: null,
                        $keyword ?: null,
                        $perPage,
                        $page);

        $paginationregist = $userModel->userregistgetAllSortedPaginated(
                                $sortDate, 
                                $fromDate ?: null, 
                                $toDate ?: null,
                                $roleSel ?: null,
                                $unitSel ?: null,
                                $jurusanSel ?: null,
                                $prodiSel ?: null,
                                $statusakunSel ?: null,
                                $keyword ?: null,
                                $perPage,
                                $page);
        
        $userregist = $paginationregist['data'];               
        $users      = $pagination['data'];

        $roleList        = $this->roleOptions();
        $unitList        = $this->unitOptions();
        $jurusanList     = $this->jurusanOptions();
        $prodiList       = $this->prodiOptions();
        $statusakunList  = $this->statusakunOptions();

        $filters = [
            'sort_date'     => $sortDate,
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'role'          => $roleSel,
            'unit'          => $unitSel,
            'jurusan'       => $jurusanSel,
            'program_studi' => $prodiSel,
            'status_akun'   => $statusakunSel,
            'keyword'       => $keyword,
        ];

        $success = Session::get('flash_success');
        $error   = Session::get('flash_error');
        Session::set('flash_success', null);
        Session::set('flash_error', null);

        require __DIR__ . '/../views/admin/data_akun.php';
    }

    #method handler buat admin update status akun user
    public function updateUserStatus()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataAkun');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $status = trim($_POST['status_akun'] ?? '');

        $allowed = ['Disetujui','Ditolak'];
        if (!$userId || !in_array($status, $allowed, true)) {
            Session::set('flash_error', 'Data tidak valid.');
            header('Location: ?route=Admin/dataAkun');
            exit;
        }

        $userModel = new User();
        $userModel->updateStatus($userId, $status);

        Session::set('flash_success', 'Status akun berhasil diperbarui.');
        header('Location: ?route=Admin/dataAkun');
        exit;
    }

    #method handler buat admin hapus user
    public function deleteUser()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataAkun');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        if (!$userId) {
            Session::set('flash_error', 'User tidak valid.');
            header('Location: ?route=Admin/dataAkun');
            exit;
        }

        $userModel = new User();
        $userModel->deleteById($userId);

        Session::set('flash_success', 'Akun berhasil dihapus.');
        header('Location: ?route=Admin/dataAkun');
        exit;
    }

    #redirect helper setelah update status booking (bisa kembali ke dashboard)
    private function redirectAfterBookingUpdate(string $redirect = ''): void
    {
        $allowed = ['Admin/dataPeminjaman','Admin/dashboard'];
        $target  = in_array($redirect, $allowed, true) ? $redirect : 'Admin/dataPeminjaman';
        header('Location: ?route=' . $target);
        exit;
    }
    
     # Helper upload gambar ruangan (opsional)
    private function handleRoomImageUpload(array $file, ?string $currentPath = null): ?string
    {
        // Jika tidak ada file baru, tetap pakai path lama
        if (!isset($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) <= 0) {
            return $currentPath;
        }

        // Simpan ke folder publik supaya bisa diakses oleh browser
        $uploadDir = __DIR__ . '/../../public/assets/image/ruangan/';
        $uploaded  = uploadFile($file, $uploadDir);

        if ($uploaded === false) {
            return $currentPath; // gagal upload, jangan kosongkan gambar lama
        }

        $newPath = 'public/assets/image/ruangan/' . $uploaded;

        // Hapus file lama kalau sebelumnya disimpan di folder yang sama
        if ($currentPath && strpos($currentPath, 'public/assets/image/ruangan/') === 0) {
            $old = __DIR__ . '/../../' . $currentPath;
            if (file_exists($old)) {
                @unlink($old);
            }
        }

        return $newPath;
    }
    
    private function feedbackOptions(): array
    {
        return[
            'Puas',
            'Tidak Puas',
        ];
    }

    private function roleOptions(): array
    {
        return[
            'Mahasiswa',
            'Dosen',
            'Tenaga Kependidikan',
        ];
    }

    private function statusakunOptions(): array
    {
        return [
            'Menunggu',
            'Ditolak',
        ];
    }

    private function unitOptions(): array
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
}