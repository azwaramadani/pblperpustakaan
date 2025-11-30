<?php
require_once __DIR__ . '/../../core/Session.php';

class AdminController {
    #method handler buat dashboard admin
    public function dashboard()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel   = new Admin();
        $bookingModel = new Booking();
        $feedbackModel= new Feedback();
        $userModel = new User();
        $roomModel = new Room();

        $adminId   = Session::get('admin_id');
        $admin     = $adminModel->findById($adminId);

        #filter data booking
        $sortDate = strtolower($_GET['sort_date'] ?? 'desc');
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';

        #filter data feedback
        $fbSortDate      = strtolower($_GET['fb_sort_date'] ?? 'desc');
        $fbSortFeedback  = strtolower($_GET['fb_sort_feedback'] ?? 'all');

        #ini buat card paling atas
        $topRooms  = $bookingModel->getTopRoomsByBooking(9);
        $bookings  = $bookingModel->getAll();
        $bookings  = $bookingModel->getAllSorted($sortDate, $fromDate ?: null, $toDate ?: null);
        $feedbacks = $feedbackModel->getAllWithRelations();

        $filters = [
            'sort_date'  => $sortDate,
            'from_date'  => $fromDate,
            'to_date'    => $toDate,
        ];

        $fbFilters = [
            'fb_sort_date'      => $fbSortDate,
            'fb_sort_feedback'  => $fbSortFeedback,
        ];

        $today = date('Y-m-d');
        $stats = [
            'user_today'        => $userModel->countRegisteredToday($today),
            'booking_today'     => $bookingModel->countBookingToday($today),
            'room_active'       => $roomModel->countActiverooms(),
            'user_total'        => $userModel->countAllusers(), 
        ];

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    #method handler buat admin kelola data bookingan user
    public function dataPeminjaman()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel   = new Admin();
        $bookingModel = new Booking();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);
        $today   = date('Y-m-d');
        $todayBookings = $bookingModel->getBookingsByDate($today);

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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataPeminjaman');
            exit;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $status    = trim($_POST['status_booking'] ?? '');

        $allowed = ['Disetujui','Ditolak','Dibatalkan','Selesai'];
        if (!$bookingId || !in_array($status, $allowed, true)) {
            Session::set('flash_error', 'Data tidak valid.');
            header('Location: ?route=Admin/dataPeminjaman');
            exit;
        }

        $bookingModel = new Booking();
        $bookingModel->updateStatus($bookingId, $status);

        Session::set('flash_success', 'Status booking berhasil diperbarui.');
        header('Location: ?route=Admin/dataPeminjaman');
        exit;
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

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);
        $users   = $userModel->getAllOrdered();

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
}