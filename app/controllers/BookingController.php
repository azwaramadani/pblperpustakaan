<?php
require_once __DIR__ . '/../../core/Session.php';
require_once __DIR__ . '/../../core/helper.php';

date_default_timezone_set('Asia/Jakarta');

Class bookingController{

    #step1 sebenernya buat buka ruangan yang dipilih aja
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
            http_response_code(404); 
            exit('Ruangan tidak ditemukan.'); 
        }
        if (!$room || strtolower($room['status'] ?? '') != 'tersedia') {
            Session::set("flash_error", "Ruangan Tidak Tersedia.");
            header('Location: ?route=User/ruangan'); 
            exit;
        }

        $user           = $userModel->findById(Session::get('user_id'));
        $puasPercent    = $feedbackModel->puasPercent($room['room_id']);
        $todayIntervals = $bookingModel->getTodayIntervalsByRoom((int)$roomId); 

        $flash = $this->getFlashMessages();
        $payload = Session::getOld(); 

        require __DIR__ . '/../views/user/booking_step1.php';
    }

    #step 2 baru method handler sebenarnya untuk semua data peminjaman mulai dari tanggal dan jam
    public function step2()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=User/step1'); 
            exit; 
            }
        
        //payload data dari form bookingstep1 (tanggal dan jam), di html pakai hidden input
        $payload = [
            'room_id'    => (int)($_POST['room_id'] ?? 0),
            'tanggal'    => trim($_POST['tanggal'] ?? ''),
            'jam_mulai'  => trim($_POST['jam_mulai'] ?? ''),
            'jam_selesai'=> trim($_POST['jam_selesai'] ?? ''),
        ];

        //validasi misal tiba-tiba tanggal dan jam peminjaman user kosong
        foreach (['room_id','tanggal','jam_mulai','jam_selesai'] as $key) {
            if (empty($payload[$key])) {
                Session::set('flash_error', 'Isi tanggal dan jam mulai/selesai.');
                Session::setOld($payload);
                header('Location: ?route=Booking/step1/'.$payload['room_id']); 
                exit;
            }
        }

        // Validasi tanggal: tidak boleh lampau dan tidak boleh Sabtu/Minggu
        $dateError = $this->validateTanggalPeminjaman($payload['tanggal']);
        if ($dateError !== null) {
            Session::set('flash_error', $dateError);
            Session::setOld($payload);
            header('Location: ?route=Booking/step1/'.$payload['room_id']);
            exit;
        }

        // Validasi jam (harus 09:00-15:00 dan mulai < selesai, gaboleh > 3 jam, gaboleh pesan jam istirahat)
        $timeError = $this->validateJamPeminjaman($payload['jam_mulai'], $payload['jam_selesai'], $payload['tanggal']);
        if ($timeError !== null) {
            Session::set('flash_error', $timeError);
            Session::setOld($payload);
            header('Location: ?route=Booking/step1/'.$payload['room_id']);
            exit;
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
            Session::set('flash_error', 'Waktu bentrok dengan peminjaman lain. Silahkan pinjam selain waktu di tabel');
            Session::setOld($payload);
            header('Location: ?route=Booking/step1/'.$payload['room_id']); 
            exit;
        }

        $user = $userModel->findById(Session::get('user_id'));

        require __DIR__ . '/../views/user/booking_step2.php';
    }

    // Method handler buat user create booking
    public function store()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse([
                'success' => false,
                'message' => 'Request tidak valid.'
            ]);
            exit;
        }

        $roomModel    = new Room();
        $bookingModel = new Booking();
        $userModel    = new User();

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

        // validasi field
        foreach (['room_id','tanggal','jam_mulai','jam_selesai','nama_penanggung_jawab','nimnip_penanggung_jawab','email_penanggung_jawab'] as $key) {
            if (empty($payload[$key])) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Lengkapi semua field.'
                ]);
                exit;
            }
        }
        
        //validasi minimal 1 anggota
        if (count($anggota) === 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Tambahkan minimal 1 anggota.'
            ]);
            exit;
        }

        //validasi supaya NIM peminjam wajib ada di database
        $invalidNims = $this->findInvalidMemberNims($anggota, $userModel);
        if (!empty($invalidNims)) {
            jsonResponse([
                'success' => false,
                'message' => 'NIM berikut tidak terdaftar: ' . implode(', ', $invalidNims)
            ]);
            exit;
        }

        //validasi tanggal tidak weekend
        $dateError = $this->validateTanggalPeminjaman($payload['tanggal']);
        if ($dateError !== null) {
            jsonResponse([
                'success' => false,
                'message' => $dateError
            ]);
            exit;
        }

        //validasi jam tidak pas istirahat, diluar jam 9.00-15.00, tidak lebih dari 3 jam juga
        $timeError = $this->validateJamPeminjaman($payload['jam_mulai'], $payload['jam_selesai'], $payload['tanggal']);
        if ($timeError !== null) {
            jsonResponse([
                'success' => false,
                'message' => $timeError
            ]);
            exit;
        }

        //validasi ruangan tiba tiba dihapus admin
        $room = $roomModel->findById($payload['room_id']);
        if (!$room) {
            jsonResponse([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ]);
            exit;
        }

        //validasi tiba-tiba ruangan diganti sama admin statusnya
        if (strtolower($room['status'] ?? '') !== 'tersedia') {
            jsonResponse([
                'success' => false,
                'message' => 'Ruangan sedang tidak tersedia.'
            ]);
            exit;
        }

        $maxCap      = (int)($room['kapasitas_max'] ?? 0);
        $minCap      = (int)($room['kapasitas_min'] ?? 0);
        $totalPeople = 1 + count($anggota);

        //validasi tiba-tiba kapasitas ruangan dikurangin sama admin
        if ($maxCap > 0 && $totalPeople > $maxCap) {
            jsonResponse([
                'success' => false,
                'message' => 'Jumlah peminjam melebihi kapasitas ruangan.'
            ]);
            exit;
        }

        //validasi tiba-tiba kapasitas ruangan ditambah sama admin
        if ($minCap > 0 && $totalPeople < $minCap) {
            jsonResponse([
                'success' => false,
                'message' => 'Jumlah peminjam belum memenuhi kapasitas minimum.'
            ]);
            exit;
        }

        //validasi waktu peminjaman bentrok sama peminjaman user lain
        if ($bookingModel->hasOverlap($payload['room_id'], $payload['tanggal'], $payload['jam_mulai'], $payload['jam_selesai'])) {
            jsonResponse([
                'success' => false,
                'message' => 'Waktu bentrok dengan peminjaman lain.'
            ]);
            exit;
        }

        //validasi gaboleh meminjam ruangan 2 kali sehari
        $nimsToCheck   = $anggota;
        $nimsToCheck[] = $payload['nimnip_penanggung_jawab'];
        foreach ($nimsToCheck as $nimCheck) {
            if ($bookingModel->memberAlreadyBooked($nimCheck, $payload['tanggal'])) {
                jsonResponse([
                    'success' => false,
                    'message' => 'NIM/NIP ' . $nimCheck . ' sudah memiliki booking pada tanggal tersebut.'
                ]);
                exit;
            }
        }

        $payload['jumlah_peminjam'] = $totalPeople;
        $payload['nimnip_peminjam'] = implode(',', $anggota);
        $payload['user_id']         = Session::get('user_id');
        $payload['status_booking']  = 'Disetujui';
        $payload['waktu_booking']   = date('Y-m-d H:i:s');
        $payload['kode_booking']    = generateBookingCode();

        //simpan ke database
        $bookingModel->createUserBooking($payload);

        jsonResponse([
            'success' => true,
            'message' => 'Booking berhasil dibuat'
        ]);

        exit;
    }

    public function adminStep1($roomId)
    {
        Session::checkAdminLogin();
        Session::preventCache();

        $adminId = Session::get('admin_id');

        $roomModel     = new Room();
        $feedbackModel = new Feedback();
        $adminModel    = new Admin();
        $bookingModel  = new Booking();

        $admin   = $adminModel->findById($adminId);
        $room    = $roomModel->findById($roomId); 

        if (!$room) { 
            http_response_code(404); 
            exit('Ruangan tidak ditemukan.'); 
        }
        if (strtolower($room['status'] ?? '') !== 'tersedia'){
            Session::set("flash_error", "Ruangan tidak tersedia! Silahkan ubah status jika ingin meminjam.");
            header('Location: ?route=Admin/dataRuangan');
            exit;
        }

        $puasPercent    = $feedbackModel->puasPercent($room['room_id']);
        $todayIntervals = $bookingModel->getTodayIntervalsByRoom((int)$roomId);

        $flash = $this->getFlashMessages();

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

        // Validasi tanggal: tidak boleh lampau dan tidak boleh Sabtu/Minggu
        $dateError = $this->validateTanggalPeminjaman($payload['tanggal']);
        if ($dateError !== null) {
            Session::set('flash_error', $dateError);
            header('Location: ?route=Booking/adminStep1/'.$payload['room_id']);
            exit;
        }

        // Validasi jam (harus 09:00-15:00 dan mulai < selesai)
        $timeError = $this->validateJamPeminjaman($payload['jam_mulai'], $payload['jam_selesai'], $payload['tanggal']);
        if ($timeError !== null) {
            Session::set('flash_error', $timeError);
            header('Location: ?route=Booking/adminStep1/'.$payload['room_id']);
            exit;
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
            Session::set('flash_error', 'Waktu bentrok dengan peminjaman lain. Silahkan pinjam selain waktu di tabel');
            header('Location: ?route=Booking/adminStep1/'.$payload['room_id']); 
            exit;
        }

        require __DIR__ . '/../views/admin/admin_bookingstep2.php';
    }

    // method handler untuk admin membuat booking
    public function adminStore()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse([
                'success' => false,
                'message' => 'Request tidak valid.'
            ]);
            exit;
        }

        $roomModel    = new Room();
        $bookingModel = new Booking();

        $anggotaInput = $_POST['nim_anggota'] ?? [];
        $anggota      = array_values(array_filter(array_map('trim', $anggotaInput), fn($v) => $v !== ''));
        $anggota      = array_unique($anggota);

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
                jsonResponse([
                    'success' => false,
                    'message' => 'Lengkapi semua field.'
                ]);
                exit;
            }
        }

        $dateError = $this->validateTanggalPeminjaman($payload['tanggal']);
        if ($dateError !== null) {
            jsonResponse([
                'success' => false,
                'message' => $dateError
            ]);
            exit;
        }

        $timeError = $this->validateJamPeminjaman($payload['jam_mulai'], $payload['jam_selesai'], $payload['tanggal']);
        if ($timeError !== null) {
            jsonResponse([
                'success' => false,
                'message' => $timeError
            ]);
            exit;
        }

        if (count($anggota) === 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Tambahkan minimal 1 anggota.'
            ]);
            exit;
        }

        $room = $roomModel->findById($payload['room_id']);
        if (!$room) {
            jsonResponse([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ]);
            exit;
        }

        $maxCap      = (int)($room['kapasitas_max'] ?? 0);
        $minCap      = (int)($room['kapasitas_min'] ?? 0);
        $totalPeople = 1 + count($anggota);

        if ($maxCap > 0 && $totalPeople > $maxCap) {
            jsonResponse([
                'success' => false,
                'message' => 'Jumlah peminjam melebihi kapasitas ruangan.'
            ]);
            exit;
        }

        if ($minCap > 0 && $totalPeople < $minCap) {
            jsonResponse([
                'success' => false,
                'message' => 'Jumlah peminjam belum memenuhi kapasitas minimum.'
            ]);
            exit;
        }

        if ($bookingModel->hasOverlap($payload['room_id'], $payload['tanggal'], $payload['jam_mulai'], $payload['jam_selesai'])) {
            jsonResponse([
                'success' => false,
                'message' => 'Waktu bentrok dengan peminjaman lain.'
            ]);
            exit;
        }

        // hanya cek anggota, bukan penanggung jawab
        foreach ($anggota as $nimCheck) {
            if ($bookingModel->memberAlreadyBooked($nimCheck, $payload['tanggal'])) {
                jsonResponse([
                    'success' => false,
                    'message' => 'NIM/NIP anggota ' . $nimCheck . ' sudah memiliki booking pada tanggal tersebut.'
                ]);
                exit;
            }
        }

        $payload['jumlah_peminjam'] = $totalPeople;
        $payload['nimnip_peminjam'] = implode(',', $anggota);

        $payload['admin_id']       = Session::get('admin_id');
        $payload['status_booking'] = 'Disetujui';
        $payload['waktu_booking']  = date('Y-m-d H:i:s');
        $payload['kode_booking']   = generateBookingCode();

        $bookingModel->createAdminBooking($payload);

        jsonResponse([
            'success' => true,
            'message' => 'Booking berhasil dibuat'
        ]);

        exit;
    }

    public function cancel($bookingId)
    {
        Session::checkUserLogin();
        Session::preventCache();
        $userId = Session::get('user_id');

        $bookingModel = new Booking();
        $userModel    = new User();
        $booking = $bookingModel->findByIdAndUser($bookingId, $userId);

        if (!$booking) {
            http_response_code(404);
            exit('Booking tidak ditemukan.');
        }

        if ($booking['status_booking'] === 'Disetujui') {
            // set status dibatalkan + catat berapa kali waktu_cancel
            $bookingModel->cancelByUser($bookingId, $userId);

            // hitung total pembatalan hari ini berdasarkan waktu_cancel
            $cancelCount = (int)$bookingModel->countCancellationsToday($userId);

            if ($cancelCount >= 3) {
                $userModel->blockUser($userId);
                Session::set('flash_error', 'Akun anda diblokir karena telah membatalkan booking 3x dalam 1 hari.');
            } else {
                Session::set('flash_success', 'Booking berhasil dibatalkan.');
                header('Location: ?route=User/riwayat');
            }
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

        //validasi booking tidak ditemukan
        if (!$booking) { 
            http_response_code(404); 
            exit('Booking tidak ditemukan.'); 
        }

        //cuman booking disetujui yang bisa diedit
        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=User/riwayat'); 
            exit;
        }

        $room        = $roomModel->findById((int)$booking['room_id']); // ruangan tetap sama tidak boleh ganti
        $puasPercent = $feedbackModel->puasPercent($booking['room_id']); //buat nampilin rating ruangan
        $user        = $userModel->findById($userId);

        $flash = $this->getFlashMessages(); 
        $old   = Session::getOld();  

        $payload = !empty($old) ? $old : [
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
            header('Location: ?route=User/riwayat'); 
            exit;
        }

        $bookingId  = (int)($_POST['booking_id'] ?? 0);
        $tanggal    = trim($_POST['tanggal'] ?? '');
        $jamMulai   = substr(trim($_POST['jam_mulai'] ?? ''), 0, 5);
        $jamSelesai = substr(trim($_POST['jam_selesai'] ?? ''), 0, 5);
        $userId     = Session::get('user_id');

        //validasi misal tanggal dan jam tiba-tiba kosong
        if (!$bookingId || !$tanggal || !$jamMulai || !$jamSelesai) {
            Session::set('flash_error', 'Lengkapi tanggal dan jam.');
            header('Location: ?route=Booking/editForm/'.$bookingId); 
            exit;
        }

        $bookingModel = new Booking();
        $roomModel    = new Room();
        $userModel    = new User();
        $feedbackModel= new Feedback();

        //validasi tiba-tiba booking tidak ditemukan
        $booking = $bookingModel->findForEdit($bookingId, $userId);
        if (!$booking) { 
            http_response_code(404); 
            exit('Booking tidak ditemukan.'); 
        }

        //validasi misal tiba-tiba status bookingnya diubah sama admin
        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=User/riwayat'); 
            exit;
        }

        // validasi ruangan gaboleh ganti
        $roomId = (int)$booking['room_id']; 
        $room   = $roomModel->findById($roomId);

        // validasi cek bentrok jadwal, exclude booking ini sendiri
        if ($bookingModel->hasOverlap($roomId, $tanggal, $jamMulai, $jamSelesai, $bookingId)) {
            Session::set('flash_error', 'Waktu bentrok dengan peminjaman lain.');
            Session::setOld([
                'booking_id' => $bookingId,
                'room_id'    => $roomId,
                'tanggal'    => $tanggal,
                'jam_mulai'  => $jamMulai,
                'jam_selesai'=> $jamSelesai,
            ]);
            header('Location: ?route=Booking/editForm/'.$bookingId);
            exit;
        }

        //payload data dari form tanggal dan jam pake hidden input di html
        $payload = [
            'booking_id' => $bookingId,
            'room_id'    => $roomId,
            'tanggal'    => $tanggal,
            'jam_mulai'  => $jamMulai,
            'jam_selesai'=> $jamSelesai,
        ];

        // initialMembers berfungsi untuk load data peminjam yang udah diisi ketika bikin booking pertama kali
        $initialMembers = $bookingModel->splitMembers($booking['nimnip_peminjam'] ?? '');
        if (empty($initialMembers)) { $initialMembers = ['']; }

        $user        = $userModel->findById($userId);
        $puasPercent = $feedbackModel->puasPercent($roomId); 

        require __DIR__ . '/../views/user/booking_step2.php';
    }

    // Simpan hasil edit (hanya tanggal/jam/email/anggota, ruangan tetap)
    public function update()
    {
        Session::checkUserLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Request tidak valid.']);
            exit;
        }

        $bookingId  = (int)($_POST['booking_id'] ?? 0);
        $tanggal    = trim($_POST['tanggal'] ?? '');
        $jamMulai   = trim($_POST['jam_mulai'] ?? '');
        $jamSelesai = trim($_POST['jam_selesai'] ?? '');
        $emailPj    = trim($_POST['email_penanggung_jawab'] ?? '');

        // NORMALISASI FORMAT JAM 
        $jamMulai   = substr($jamMulai, 0, 5);
        $jamSelesai = substr($jamSelesai, 0, 5);

        $anggotaInput = $_POST['nim_anggota'] ?? [];
        $anggota      = array_values(array_filter(array_map('trim', $anggotaInput), fn($v) => $v !== ''));

        $userId       = Session::get('user_id');
        $bookingModel = new Booking();
        $roomModel    = new Room();

        $booking = $bookingModel->findForEdit($bookingId, $userId);
        if (!$booking) {
            jsonResponse(['success' => false, 'message' => 'Booking tidak ditemukan.']);
            exit;
        }

        if ($booking['status_booking'] !== 'Disetujui') {
            jsonResponse(['success' => false, 'message' => 'Tidak bisa edit booking ini.']);
            exit;
        }

        $roomId = (int)$booking['room_id'];
        $room   = $roomModel->findById($roomId);

        if (!$room) {
            jsonResponse([
                'success' => false, 
                'message' => 'Ruangan tidak ditemukan.']);
            exit;
        }

        if (strtolower($room['status'] ?? '') !== 'tersedia') {
            jsonResponse([
                'success' => false,
                'message' => 'Ruangan sedang tidak tersedia.'
            ]);
            exit; 
        }

        // VALIDASI STEP 1
        if (!$tanggal || !$jamMulai || !$jamSelesai) {
            jsonResponse(['success' => false, 'message' => 'Lengkapi tanggal dan jam.']);
            exit;
        }

        $dateError = $this->validateTanggalPeminjaman($tanggal);
        if ($dateError) {
            jsonResponse(['success' => false, 'message' => $dateError]);
            exit;
        }

        $timeError = $this->validateJamPeminjaman($jamMulai, $jamSelesai, $tanggal);
        if ($timeError) {
            jsonResponse(['success' => false, 'message' => $timeError]);
            exit;
        }

        if ($bookingModel->hasOverlap($roomId, $tanggal, $jamMulai, $jamSelesai, $bookingId)) {
            jsonResponse(['success' => false, 'message' => 'Waktu bentrok dengan peminjaman lain.']);
            exit;
        }

        // VALIDASI STEP 2
        if (!$emailPj || !filter_var($emailPj, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Email tidak valid.']);
            exit;
        }

        $userModel = new User();

        $invalidNims = $this->findInvalidMemberNims($anggota, $userModel);

        if (!empty($invalidNims)) {
            jsonResponse([
                'success' => false,
                'message' => 'NIM berikut tidak terdaftar: ' . implode(', ', $invalidNims)
            ]);
            exit;
        }

        if (count($anggota) === 0) {
            jsonResponse(['success' => false, 'message' => 'Minimal 1 anggota.']);
            exit;
        }

        if (count($anggota) !== count(array_unique($anggota))) {
            jsonResponse(['success' => false, 'message' => 'NIM anggota tidak boleh sama.']);
            exit;
        }

        // KAPASITAS
        $maxCap      = (int)($room['kapasitas_max'] ?? 0);
        $minCap      = (int)($room['kapasitas_min'] ?? 0);
        $totalPeople = 1 + count($anggota);

        if ($maxCap > 0 && $totalPeople > $maxCap) {
            jsonResponse(['success' => false, 'message' => 'Melebihi kapasitas ruangan.']);
            exit;
        }

        if ($minCap > 0 && $totalPeople < $minCap) {
            jsonResponse(['success' => false, 'message' => 'Belum memenuhi kapasitas minimum.']);
            exit;
        }

        // SAVE
        $payload = [
            'room_id'                => $roomId,
            'tanggal'                => $tanggal,
            'jam_mulai'              => $jamMulai,
            'jam_selesai'            => $jamSelesai,
            'jumlah_peminjam'        => $totalPeople,
            'nimnip_peminjam'        => implode(',', $anggota),
            'email_penanggung_jawab' => $emailPj,
        ];

        $bookingModel->updateByUser($bookingId, $userId, $payload);

        jsonResponse([
            'success' => true,
            'message' => 'Booking berhasil diperbarui.'
        ]);

        exit;
    }

    public function adminEditForm($bookingId)
    {
        Session::checkAdminLogin();
        Session::preventCache();
        
        $bookingId = (int)$bookingId;
        $adminId   = Session::get('admin_id');

        $bookingModel  = new Booking();
        $roomModel     = new Room();
        $adminModel    = new Admin();
        $feedbackModel = new Feedback();

        $booking = $bookingModel->findForEdit($bookingId, $adminId);

        if (!$booking) { 
            http_response_code(404); 
            exit('Booking tidak ditemukan.'); 
        }
        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=Admin/dataFromAdminCreateBooking'); 
            exit;
        }

        $room        = $roomModel->findById((int)$booking['room_id']); // ruangan tetap
        $puasPercent = $feedbackModel->puasPercent($booking['room_id']);
        $admin       = $adminModel->findById($adminId);

        // Data lama untuk isi otomatis form
        $payload = [
            'booking_id' => $booking['booking_id'],
            'room_id'    => $booking['room_id'],
            'tanggal'    => $booking['tanggal'],
            'jam_mulai'  => $booking['jam_mulai'],
            'jam_selesai'=> $booking['jam_selesai'],
        ];

        require __DIR__ . '/../views/admin/admin_bookingstep1.php';
    }

    public function adminEditStep2()
    {
        Session::checkAdminLogin();
        Session::preventCache();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=Admin/dataFromAdminCreateBooking'); 
            exit;
        }

        $bookingId  = (int)($_POST['booking_id'] ?? 0);
        $tanggal    = trim($_POST['tanggal'] ?? '');
        $jamMulai   = trim($_POST['jam_mulai'] ?? '');
        $jamSelesai = trim($_POST['jam_selesai'] ?? '');
        $adminId    = Session::get('admin_id');

        if (!$bookingId || !$tanggal || !$jamMulai || !$jamSelesai) {
            Session::set('flash_error', 'Lengkapi tanggal dan jam.');
            header('Location: ?route=Booking/adminEditForm/'.$bookingId); 
            exit;
        }

        $bookingModel = new Booking();
        $roomModel    = new Room();
        $adminModel   = new Admin();
        $feedbackModel= new Feedback();

        $booking = $bookingModel->findForEdit($bookingId, $adminId);
        if (!$booking) { 
            http_response_code(404); 
            exit('Booking tidak ditemukan.'); 
        }

        if ($booking['status_booking'] !== 'Disetujui') {
            Session::set('flash_error', 'Hanya booking berstatus Disetujui yang bisa diubah.');
            header('Location: ?route=Admin/dataFromAdminCreateBooking'); 
            exit;
        }

        // ruangan tetap
        $roomId = (int)$booking['room_id']; 
        $room   = $roomModel->findById($roomId);

        // Cek bentrok jadwal; exclude booking ini sendiri
        if ($bookingModel->hasOverlap($roomId, $tanggal, $jamMulai, $jamSelesai, $bookingId)) {
            Session::set('flash_error', 'Waktu bentrok dengan peminjaman lain.');
            header('Location: ?route=Booking/adminEditForm/'.$bookingId); 
            exit;
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

        $admin = $adminModel->findById($adminId);
        $puasPercent = $feedbackModel->puasPercent($roomId); 

        require __DIR__ . '/../views/admin/admin_bookingstep2.php';
    }


    private function findInvalidMemberNims(array $anggota, User $userModel): array
    {
        // Hilangkan duplikat supaya pengecekan database tidak berulang.
        $uniqueNims = array_values(array_unique($anggota));

        $invalid = [];
        foreach ($uniqueNims as $nim) {
            // Lewati nilai kosong (jaga-jaga kalau input tidak bersih).
            if ($nim === '') {
                continue;
            }

            // Jika NIM/NIP tidak ada di tabel user, simpan untuk pesan error.
            if (!$userModel->isNIMExists($nim)) {
                $invalid[] = $nim;
            }
        }

        return $invalid;
    }

    
    private function validateTanggalPeminjaman(string $tanggal): ?string
    {
        $tz = new DateTimeZone('Asia/Jakarta');
        $date = DateTime::createFromFormat('Y-m-d', $tanggal, $tz);

        // Jika format salah, anggap tidak valid
        if (!$date || $date->format('Y-m-d') !== $tanggal) {
            return 'Tanggal peminjaman tidak boleh sebelum hari ini.';
        }

        // Set jam ke 00:00 supaya perbandingan adil
        $date->setTime(0, 0, 0);
        $today = new DateTime('today', $tz);

        if ($date < $today) {
            return 'Tanggal peminjaman tidak boleh sebelum hari ini.';
        }

        $dayNumber = (int)$date->format('N'); // 1=Senin ... 6=Sabtu, 7=Minggu
        if ($dayNumber >= 6) {
            return 'Peminjaman tidak diperbolehkan pada hari Sabtu dan Minggu.';
        }

        return null;
    }


    private function validateJamPeminjaman(string $jamMulai, string $jamSelesai, string $tanggal): ?string
    {
            $tz = new DateTimeZone('Asia/Jakarta');
            $start = DateTime::createFromFormat('H:i', $jamMulai, $tz);
            $end   = DateTime::createFromFormat('H:i', $jamSelesai, $tz);

            if (!$start || !$end) {
                return 'Format jam tidak valid.';
            }

            $allowedStart = DateTime::createFromFormat('H:i', '09:00', $tz);
            $allowedEnd   = DateTime::createFromFormat('H:i', '15:00', $tz);

            if ($start < $allowedStart || $end > $allowedEnd) {
                return 'Peminjaman hanya boleh antara 09:00 - 15:00.';
            }

            if ($end <= $start) {
                return 'Jam selesai harus setelah jam mulai.';
            }

            $diffMinutes = ($end->getTimestamp() - $start->getTimestamp()) / 60;

            if ($diffMinutes > 180) {
                return 'Durasi peminjaman maksimal 3 jam.';
            }

            // BLOK JAM ISTIRAHAT
            $breakStart = DateTime::createFromFormat('H:i', '11:30', $tz);
            $breakEnd   = DateTime::createFromFormat('H:i', '12:30', $tz);

            if ($start >= $breakStart && $end <= $breakEnd) {
                return 'Tidak bisa memesan ruangan untuk jam istirahat (11:30 - 12:30).';
            }

            // BLOK JAM YANG SUDAH LEWAT HARI INI
            if ($tanggal === date('Y-m-d')) {
                $now = new DateTime('now', $tz);

                if ($start < $now) {
                    return 'Tidak bisa memesan jam yang sudah berlalu.';
                }
            }

            return null;
    }

    private function getFlashMessages(){
        return [
            'success'   => Session::flash('flash_success'),
            'error'     => Session::flash('flash_error')
        ];
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}