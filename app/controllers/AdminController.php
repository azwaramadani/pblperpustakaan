<?php
require_once __DIR__ . '/../../core/Session.php';

class AdminController {
    public function dashboard()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminModel   = new Admin();
        $bookingModel = new Booking();
        $feedbackModel= new Feedback();

        $adminId   = Session::get('admin_id');
        $admin     = $adminModel->findById($adminId);
        $topRooms  = $bookingModel->getTopRoomsByBooking(5);
        $bookings  = $bookingModel->getAll();
        $feedbacks = $feedbackModel->getAllWithRelations();

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    
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
}