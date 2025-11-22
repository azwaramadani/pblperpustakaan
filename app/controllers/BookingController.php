<?php
// proses_booking.php
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../core/helper.php';

Class bookingController{
     public function step1($roomId)
    {
        Session::checkUserLogin();
        Session::preventCache();

        $roomModel = new Room();
        $room = $roomModel->findById($roomId);
        if (!$room) {
            http_response_code(404);
            exit('Ruangan tidak ditemukan.');
        }

        $userModel     = new User();
        $feedbackModel = new Feedback();

        $user         = $userModel->findById(Session::get('user_id'));
        $puasPercent  = $feedbackModel->puasPercent($room['room_id']);

        $data = [
            'room'         => $room,
            'user'         => $user,
            'puas_percent' => $puasPercent
        ];

        require __DIR__ . '/../views/user/booking_step1.php';
    }

    // Terima tanggal + jam dari step1, lalu tampilkan form detail di step2
    public function step2()
    {
        Session::checkUserLogin();
        Session::preventCache();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?route=User/ruangan'); exit; }

        $payload = [
            'room_id'    => (int)($_POST['room_id'] ?? 0),
            'tanggal'    => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'  => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'=> trim($_POST['jam_selesai'] ?? ''),
        ];
        foreach (['room_id','tanggal','jam_mulai','jam_selesai'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Isi tanggal dan jam mulai/selesai.');
                header('Location: ?route=Booking/step1/'.$payload['room_id']); exit;
            }
        }

        $roomModel = new Room();
        $room = $roomModel->findById($payload['room_id']);
        if (!$room) { http_response_code(404); exit('Ruangan tidak ditemukan.'); }

        $bookingModel = new Booking();
        if ($bookingModel->hasOverlap($payload['room_id'], $payload['tanggal'], $payload['jam_mulai'], $payload['jam_selesai'])){
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/step1/'.$payload['room_id']); exit;
        }

        $userModel = new User();
        $user = $userModel->findById(Session::get('user_id'));

        $data = ['room' => $room, 'payload' => $payload, 'user' => $user];
        require __DIR__ . '/../views/user/booking_step2.php';
    }

    // Simpan booking
    public function store()
    {
        Session::checkUserLogin();
        Session::preventCache();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/ruangan');
            exit;
        }

        $payload = [
            'room_id'                 => (int)($_POST['room_id'] ?? 0),
            'tanggal'                 => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'               => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'             => trim($_POST['jam_selesai'] ?? ''),
            'jumlah_peminjam'        => (int)($_POST['jumlah_peminjam'] ?? 0),
            'nama_penanggung_jawab'   => trim($_POST['nama_penanggung_jawab'] ?? ''),
            'nimnip_penanggung_jawab' => trim($_POST['nimnip_penanggung_jawab'] ?? ''),
            'email_penanggung_jawab'  => trim($_POST['email_penanggung_jawab'] ?? ''),
            'nimnip_peminjam'         => trim($_POST['nimnip_peminjam'] ?? ''),
        ];

        foreach (['room_id','tanggal','jam_mulai','jam_selesai','nama_penanggung_jawab','nimnip_penanggung_jawab','email_penanggung_jawab','nimnip_peminjam'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Lengkapi semua field.');
                header('Location: ?route=Booking/step1/'.$payload['room_id']);
                exit;
            }
        }

        $roomModel = new Room();
        $room = $roomModel->findById($payload['room_id']);
        if (!$room) {
            http_response_code(404);
            exit('Ruangan tidak ditemukan.');
        }

        $bookingModel = new Booking();
        if ($bookingModel->hasOverlap($payload['room_id'], $payload['tanggal'], $payload['jam_mulai'], $payload['jam_selesai'])) {
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/step1/'.$payload['room_id']);
            exit;
        }

        $payload['user_id']        = Session::get('user_id');
        $payload['status_booking'] = 'Disetujui';
        $payload['waktu_booking']  = date('Y-m-d H:i:s');
        $payload['kode_booking']   = generateBookingCode();

        $bookingModel->createUserBooking($payload);

        Session::set('flash_success', 'Booking berhasil dibuat.');
        header('Location: ?route=User/riwayat');
        exit;
    }

    public function cancel($bookingId)
    {
        Session::checkUserLogin();
        Session::preventCache();
        $userId = Session::get('user_id');

        $bookingModel = new Booking();
        $booking = $bookingModel->findByIdAndUser($bookingId, $userId);

        if (!$booking) {
            http_response_code(404);
            exit('Booking tidak ditemukan.');
        }

        if ($booking['status_booking'] === 'Disetujui') {
            $bookingModel->cancelByUser($bookingId, $userId);
            Session::set('flash_success', 'Booking berhasil dibatalkan.');
        } else {
            Session::set('flash_error', 'Booking tidak bisa dibatalkan untuk status ini.');
        }

        header('Location: ?route=User/riwayat');
        exit;
    }

}