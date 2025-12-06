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

        #buat set auto selesai kalu jam booking udah nyentuh jam selesai
        $bookingModel->markFinishedBookings();

        #filter dashboard data booking
        $sortCreate = strtolower($_GET['sort_create'] ?? 'desc');
        $roleSel    = $_GET['role'] ?? '';
        $unitSel    = $_GET['unit'] ?? '';
        $jurusanSel = $_GET['jurusan'] ?? '';
        $prodiSel   = $_GET['program_studi'] ?? '';
        $keyword    = trim($_GET['keyword'] ?? '');
        
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
        
        $todayBookings = $pagination['data'];    
    
        $roleList    = $this->roleOptions();
        $unitList    = $this->unitOptions();
        $jurusanList = $this->jurusanOptions();
        $prodiList   = $this->prodiOptions();

        $filters = [
            'sort_create'   => $sortCreate,
            'role'          => $roleSel,
            'unit'          => $unitSel,
            'jurusan'       => $jurusanSel,
            'program_studi' => $prodiSel,
            'keyword'       => $keyword,
        ];

        #data tabel dashboard admin ruangan populer dan feedback user
        $topRooms  = $bookingModel->getTopRoomsByBooking(9);
        #filter data feedback
        $fbSortDate      = strtolower($_GET['fb_sort_date'] ?? 'desc');
        $fbSortFeedback  = strtolower($_GET['fb_sort_feedback'] ?? 'all');
        $feedbacks       = $feedbackModel->getAllWithFilters($fbSortDate, $fbSortFeedback);

        $fbFilters = [
            'fb_sort_date'      => $fbSortDate,
            'fb_sort_feedback'  => $fbSortFeedback,
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