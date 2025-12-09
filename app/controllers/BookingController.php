<?php
// proses_booking.php
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../core/helper.php';

Class bookingController{
     public function step1($roomId)
    {
        Session::checkUserLogin();
        Session::preventCache();

        $roomModel     = new Room();
        $userModel     = new User();
        $feedbackModel = new Feedback();
        $bookingModel  = new Booking();

        $room = $roomModel->findById($roomId);      
        if (!$room) { 
            http_response_code(404); exit('Ruangan tidak ditemukan.'); 
        }
        if (!$room || strtolower($room['status'] ?? '') != 'tersedia') {
            header('Location: ?route=User/ruangan'); exit;
        }

        $user           = $userModel->findById(Session::get('user_id'));
        $puasPercent    = $feedbackModel->puasPercent($room['room_id']);
        $todayIntervals = $bookingModel->getTodayIntervalsByRoom((int)$roomId); 

        require __DIR__ . '/../views/user/booking_step1.php';
    }

    // Terima tanggal + jam dari step1, lalu tampilkan form detail di step2
    public function step2()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/ruangan'); 
            exit; 
            }

        $payload = [
            'room_id'    => (int)($_POST['room_id'] ?? 0),
            'tanggal'    => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'  => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'=> trim($_POST['jam_selesai'] ?? ''),
        ];

        foreach (['room_id','tanggal','jam_mulai','jam_selesai'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Isi tanggal dan jam mulai/selesai.');
                header('Location: ?route=Booking/step1/'.$payload['room_id']); 
                exit;
            }
        }

        $userModel    = new User();
        $roomModel    = new Room();
        $bookingModel = new Booking();

        $room = $roomModel->findById($payload['room_id']);
        if (!$room) { http_response_code(404); exit('Ruangan tidak ditemukan.'); }

        // cek bentrok jadwal
        if ($bookingModel->hasOverlap(
                $payload['room_id'], 
                $payload['tanggal'], 
                $payload['jam_mulai'], 
                $payload['jam_selesai']
        )){
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/step1/'.$payload['room_id']); 
            exit;
        }

        $user = $userModel->findById(Session::get('user_id'));

        require __DIR__ . '/../views/user/booking_step2.php';
    }

    public function adminStep1($roomId)
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $roomModel     = new Room();
        $feedbackModel = new Feedback();
        $adminModel    = new Admin();
        $bookingModel  = new Booking();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);
        
        $room    = $roomModel->findById($roomId);        
        if (!$room) { 
            http_response_code(404); exit('Ruangan tidak ditemukan.'); 
        }
        if (!$room || strtolower($room['status'] ?? '') != 'tersedia') {
            header('Location: ?route=User/ruangan'); exit;
        }

        $adminId        = Session::get('admin_id');
        $puasPercent    = $feedbackModel->puasPercent($room['room_id']);
        $todayIntervals = $bookingModel->getTodayIntervalsByRoom((int)$roomId);

        require __DIR__ . '/../views/admin/admin_bookingstep1.php';
    }

    public function adminStep2()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataRuangan'); 
            exit; 
            }

        $payload = [
            'room_id'    => (int)($_POST['room_id'] ?? 0),
            'tanggal'    => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'  => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'=> trim($_POST['jam_selesai'] ?? ''),
        ];

        foreach (['room_id','tanggal','jam_mulai','jam_selesai'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Isi tanggal dan jam mulai/selesai.');
                header('Location: ?route=Booking/adminStep1/'.$payload['room_id']); 
                exit;
            }
        }

        $roomModel    = new Room();
        $bookingModel = new Booking();
        $adminModel   = new Admin();

        $adminId = Session::get('admin_id');
        $admin   = $adminModel->findById($adminId);

        $room = $roomModel->findById($payload['room_id']);
        if (!$room) { http_response_code(404); exit('Ruangan tidak ditemukan.'); }

        // cek bentrok jadwal
        if ($bookingModel->hasOverlap(
                $payload['room_id'], 
                $payload['tanggal'], 
                $payload['jam_mulai'], 
                $payload['jam_selesai']
        )){
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/adminStep1/'.$payload['room_id']); 
            exit;
        }

        $adminId = $adminModel->findById(Session::get('admin_id'));

        require __DIR__ . '/../views/admin/admin_bookingstep2.php';
    }

    // method handler admin create booking
    public function adminStore()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=admin/dataRuangan');
            exit;
        }

        $roomModel    = new Room();
        $bookingModel = new Booking();

        $anggotaInput = $_POST['nim_anggota'] ?? [];
        $anggota      = array_values(array_filter(array_map('trim', $anggotaInput), fn($v) => $v !== ''));

        $payload = [
            'room_id'                 => (int)($_POST['room_id'] ?? 0),
            'tanggal'                 => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'               => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'             => trim($_POST['jam_selesai'] ?? ''),
            'jumlah_peminjam'         => (int)($_POST['jumlah_peminjam'] ?? 0),
            'nama_penanggung_jawab'   => trim($_POST['nama_penanggung_jawab'] ?? ''),
            'nimnip_penanggung_jawab' => trim($_POST['nimnip_penanggung_jawab'] ?? ''),
            'email_penanggung_jawab'  => trim($_POST['email_penanggung_jawab'] ?? ''),
        ];

        foreach (['room_id','tanggal','jam_mulai','jam_selesai','nama_penanggung_jawab','nimnip_penanggung_jawab','email_penanggung_jawab'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Lengkapi semua field.');
                header('Location: ?route=Booking/adminStep1/'.$payload['room_id']);
                exit;
            }
        }

        // Minimal 1 anggota
        if (count($anggota) === 0) {
            Session::set('flash_error', 'Tambahkan minimal 1 NIM/NIP anggota.');
            header('Location: ?route=Booking/step1/' . $payload['room_id']);
            exit;
        }

        $room = $roomModel->findById($payload['room_id']);
        if (!$room) {
            http_response_code(404);
            exit('Ruangan tidak ditemukan.');
        }

        if ($bookingModel->hasOverlap($payload['room_id'], $payload['tanggal'], $payload['jam_mulai'], $payload['jam_selesai'])) {
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/step1/'.$payload['room_id']);
            exit;
        }

        // Cek setiap NIM (penanggung + semua anggota) supaya tidak double-book tanggal & ruangan yang sama
        $nimsToCheck   = $anggota;
        $nimsToCheck[] = $payload['nimnip_penanggung_jawab'];

        foreach ($nimsToCheck as $nimCheck) {
            if ($bookingModel->memberAlreadyBooked($nimCheck, $payload['room_id'], $payload['tanggal'])) {
                Session::set('flash_error', 'NIM/NIP ' . $nimCheck . ' sudah terdaftar di ruangan ini pada tanggal tersebut.');
                header('Location: ?route=Booking/step1/' . $payload['room_id']);
                exit;
            }
        }

        $payload['jumlah_peminjam'] = 1 + count($anggota);
        // Simpan semua NIM anggota ke satu kolom nimnip_peminjam (dipisah koma)
        $payload['nimnip_peminjam'] = implode(',', $anggota);
        
        $payload['admin_id']       = Session::get('admin_id');
        $payload['status_booking'] = 'Disetujui';
        $payload['waktu_booking']  = date('Y-m-d H:i:s');
        $payload['kode_booking']   = generateBookingCode();

        $bookingModel->createAdminBooking($payload);

        Session::set('flash_success', 'Booking berhasil dibuat.');
        header('Location: ?route=Admin/dataFromAdminCreateBooking');
        exit;
    }

    // Method handler user create booking
    public function store()
    {
        Session::checkUserLogin();
        Session::preventCache();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/ruangan');
            exit;
        }

        $roomModel    = new Room();
        $bookingModel = new Booking();

        $anggotaInput = $_POST['nim_anggota'] ?? [];
        $anggota      = array_values(array_filter(array_map('trim', $anggotaInput), fn($v) => $v !== ''));

        $payload = [
            'room_id'                 => (int)($_POST['room_id'] ?? 0),
            'tanggal'                 => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'               => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'             => trim($_POST['jam_selesai'] ?? ''),
            'jumlah_peminjam'         => (int)($_POST['jumlah_peminjam'] ?? 0),
            'nama_penanggung_jawab'   => trim($_POST['nama_penanggung_jawab'] ?? ''),
            'nimnip_penanggung_jawab' => trim($_POST['nimnip_penanggung_jawab'] ?? ''),
            'email_penanggung_jawab'  => trim($_POST['email_penanggung_jawab'] ?? ''),
        ];

        foreach (['room_id','tanggal','jam_mulai','jam_selesai','nama_penanggung_jawab','nimnip_penanggung_jawab','email_penanggung_jawab'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Lengkapi semua field.');
                header('Location: ?route=Booking/step1/'.$payload['room_id']);
                exit;
            }
        }

        // Minimal 1 anggota
        if (count($anggota) === 0) {
            Session::set('flash_error', 'Tambahkan minimal 1 NIM/NIP anggota.');
            header('Location: ?route=Booking/step1/' . $payload['room_id']);
            exit;
        }

        $room = $roomModel->findById($payload['room_id']);
        if (!$room) {
            http_response_code(404);
            exit('Ruangan tidak ditemukan.');
        }

        if ($bookingModel->hasOverlap($payload['room_id'], $payload['tanggal'], $payload['jam_mulai'], $payload['jam_selesai'])) {
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/step1/'.$payload['room_id']);
            exit;
        }

        // Cek setiap NIM (penanggung + semua anggota) supaya tidak double-book tanggal & ruangan yang sama
        $nimsToCheck   = $anggota;
        $nimsToCheck[] = $payload['nimnip_penanggung_jawab'];

        foreach ($nimsToCheck as $nimCheck) {
            if ($bookingModel->memberAlreadyBooked($nimCheck, $payload['room_id'], $payload['tanggal'])) {
                Session::set('flash_error', 'NIM/NIP ' . $nimCheck . ' sudah terdaftar di ruangan ini pada tanggal tersebut.');
                header('Location: ?route=Booking/step1/' . $payload['room_id']);
                exit;
            }
        }

        // buat count otomatis jumlah_peminjam walaupun NIM anggota yang diinput user ga sesuai sama jumlah peminjam yang dia input
        $payload['jumlah_peminjam'] = 1 + count($anggota);

        // Simpan semua NIM anggota ke satu kolom nimnip_peminjam (dipisah koma)
        $payload['nimnip_peminjam'] = implode(',', $anggota);
        $payload['user_id']         = Session::get('user_id');
        $payload['status_booking']  = 'Disetujui';
        $payload['waktu_booking']   = date('Y-m-d H:i:s');
        $payload['kode_booking']    = generateBookingCode();

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

    // Step 1 edit: preload data lama, user hanya boleh ubah tanggal/jam
    public function editForm($bookingId)
    {
        Session::checkUserLogin();
        Session::preventCache();

        $bookingId = (int)$bookingId;
        $userId    = Session::get('user_id');

        $bookingModel  = new Booking();
        $roomModel     = new Room();
        $userModel     = new User();
        $feedbackModel = new Feedback();

        $booking = $bookingModel->findForEdit($bookingId, $userId);
        if (!$booking) { http_response_code(404); exit('Booking tidak ditemukan.'); }
        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=User/riwayat'); exit;
        }

        $room        = $roomModel->findById((int)$booking['room_id']); // ruangan tetap
        $puasPercent = $feedbackModel->puasPercent($booking['room_id']);
        $user        = $userModel->findById($userId);

        // Data lama untuk isi otomatis form
        $payload = [
            'booking_id' => $booking['booking_id'],
            'room_id'    => $booking['room_id'],
            'tanggal'    => $booking['tanggal'],
            'jam_mulai'  => $booking['jam_mulai'],
            'jam_selesai'=> $booking['jam_selesai'],
        ];

        require __DIR__ . '/../views/user/booking_step1.php';
    }

     // Step 2 edit: cek bentrok, preload anggota, room tetap dari booking lama
    public function editStep2()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/riwayat'); exit;
        }

        $bookingId  = (int)($_POST['booking_id'] ?? 0);
        $tanggal    = trim($_POST['tanggal'] ?? '');
        $jamMulai   = trim($_POST['jam_mulai'] ?? '');
        $jamSelesai = trim($_POST['jam_selesai'] ?? '');
        $userId     = Session::get('user_id');

        if (!$bookingId || !$tanggal || !$jamMulai || !$jamSelesai) {
            Session::set('flash_error', 'Lengkapi tanggal dan jam.');
            header('Location: ?route=Booking/editForm/'.$bookingId); exit;
        }

        $bookingModel = new Booking();
        $roomModel    = new Room();
        $userModel    = new User();
        $feedbackModel= new Feedback();

        $booking = $bookingModel->findForEdit($bookingId, $userId);
        if (!$booking) { http_response_code(404); exit('Booking tidak ditemukan.'); }
        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=User/riwayat'); exit;
        }

        $roomId = (int)$booking['room_id']; // ruangan tetap
        $room   = $roomModel->findById($roomId);

        // Cek bentrok jadwal; exclude booking ini sendiri
        if ($bookingModel->hasOverlap($roomId, $tanggal, $jamMulai, $jamSelesai, $bookingId)) {
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/editForm/'.$bookingId); exit;
        }

        $payload = [
            'booking_id' => $bookingId,
            'room_id'    => $roomId,
            'tanggal'    => $tanggal,
            'jam_mulai'  => $jamMulai,
            'jam_selesai'=> $jamSelesai,
        ];

        // Isi awal daftar anggota dari data lama
        $initialMembers = $bookingModel->splitMembers($booking['nimnip_peminjam'] ?? '');
        if (empty($initialMembers)) { $initialMembers = ['']; }

        $user        = $userModel->findById($userId);
        $puasPercent = $feedbackModel->puasPercent($roomId); // opsional jika mau dipakai lagi

        require __DIR__ . '/../views/user/booking_step2.php';
    }

    // Simpan hasil edit (hanya tanggal/jam/email/anggota, ruangan tetap)
    public function update()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/riwayat'); exit;
        }

        $bookingId  = (int)($_POST['booking_id'] ?? 0);
        $tanggal    = trim($_POST['tanggal'] ?? '');
        $jamMulai   = trim($_POST['jam_mulai'] ?? '');
        $jamSelesai = trim($_POST['jam_selesai'] ?? '');
        $emailPj    = trim($_POST['email_penanggung_jawab'] ?? '');

        $anggotaInput = $_POST['nim_anggota'] ?? [];
        $anggota      = array_values(array_filter(array_map('trim', $anggotaInput), fn($v) => $v !== ''));

        if (!$bookingId || !$tanggal || !$jamMulai || !$jamSelesai || !$emailPj) {
            Session::set('flash_error', 'Lengkapi semua field.');
            header('Location: ?route=Booking/editForm/'.$bookingId); exit;
        }
        if (count($anggota) === 0) {
            Session::set('flash_error', 'Tambahkan minimal 1 NIM/NIP anggota.');
            header('Location: ?route=Booking/editForm/'.$bookingId); exit;
        }

        $userId       = Session::get('user_id');
        $bookingModel = new Booking();
        $roomModel    = new Room();

        $booking = $bookingModel->findForEdit($bookingId, $userId);
        if (!$booking) { http_response_code(404); exit('Booking tidak ditemukan.'); }
        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=User/riwayat'); exit;
        }

        $roomId = (int)$booking['room_id']; // ruangan tidak boleh diganti
        $room   = $roomModel->findById($roomId);
        if (!$room) {
            Session::set('flash_error', 'Ruangan tidak ditemukan.');
            header('Location: ?route=Booking/editForm/'.$bookingId); exit;
        }

        if ($bookingModel->hasOverlap($roomId, $tanggal, $jamMulai, $jamSelesai, $bookingId)) {
            Session::set('flash_error', 'Waktu bentrok dengan booking lain.');
            header('Location: ?route=Booking/editForm/'.$bookingId); exit;
        }

        $payload = [
            'room_id'                => $roomId, // fixed, tidak diubah user
            'tanggal'                => $tanggal,
            'jam_mulai'              => $jamMulai,
            'jam_selesai'            => $jamSelesai,
            'jumlah_peminjam'        => 1 + count($anggota), // 1 penanggung + anggota
            'nimnip_peminjam'        => implode(',', $anggota),
            'email_penanggung_jawab' => $emailPj,
        ];

        $bookingModel->updateByUser($bookingId, $userId, $payload);

        Session::set('flash_success', 'Booking berhasil diperbarui.');
        header('Location: ?route=User/riwayat');
        exit;
    }

}