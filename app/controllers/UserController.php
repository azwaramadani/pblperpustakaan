<?php
require_once __DIR__ . '/../../core/Session.php';

class UserController{
    public function home()
    {
        Session::checkUserLogin();
        Session::preventCache();
        if (!Session::get('user_id')) {
            header("Location: ?route=Auth/login");
            exit;
        }

        $userModel    = new User();
        $roomModel    = new Room();
        $bookingModel = new Booking();

        $user_id  = Session::get('user_id');
        $user     = $userModel->findById($user_id);
        $toprooms = $bookingModel->getTopRoomsbyBooking(3);
        $rooms    = $roomModel->getAll();

        require __DIR__ . '/../views/user/home.php';
    }

    public function ruangan()
    {
        Session::checkUserLogin();
        Session::preventCache();
    
        $userModel    = new User();
        $roomModel    = new Room();
        $bookingModel = new Booking();

        $user        = $userModel->findById(Session::get('user_id'));
        $rooms       = $roomModel->getAll();
        $busyRoomIds = $bookingModel->getBusyRoomIdsNow();
        
        //buat set status ruangan jadi dipinjam kalo jam_mulai <= current_time <= jam_selesai 
        foreach ($rooms as &$room) {
            $statusRaw  =  strtolower($room['status'] ?? '');
            
            // Jika ruangan available dan sedang ada booking aktif, tunjukkan "Sedang Dipinjam"
            if (in_array((int)$room['room_id'], $busyRoomIds, true) && $statusRaw === 'tersedia') {
                $room['status_display'] = 'Sedang Dipinjam';
                $room['status_class']   = 'borrowed';
            } else {
                $room['status_display'] = $room['status'] ?? 'Tidak Diketahui';
                $room['status_class']   = ($statusRaw === 'tersedia') ? 'available' : 'unavailable';
            }
        }
        unset($room); // hindari reference leak

        require __DIR__ . '/../views/user/ruangan.php';
    }

    public function riwayat()
    {
        Session::checkUserLogin();
        Session::preventCache();

        $userModel    = new User();
        $bookingModel = new Booking();

        $userId     = Session::get('user_id');
        $user       = $userModel->findById($userId);
        $bookingModel->markFinishedBookings();
        $riwayatRaw = $bookingModel->getHistoryByUser($userId);

        //proses data riwayat
        $riwayat    = [];
        
        foreach ($riwayatRaw as $row) {
            $bookingId     = $row['booking_id'];
            $namaRuangan   = $row['nama_ruangan'] ?? '-';
            $kodeBooking   = $row['kode_booking'] ?? '-';
            $tanggal       = $this->formatTanggal($row['tanggal'] ?? null);
            $jam           = $this->formatRentangJam($row['jam_mulai'] ?? null, $row['jam_selesai'] ?? null);
            $penanggung    = $row['nama_penanggung_jawab'] ?? '-';
            $nim           = $row['nimnip_penanggung_jawab'] ?? '-';
            $email         = $row['email_penanggung_jawab'] ?? '-';
            $nimRuangan    = $row['nimnip_peminjam'] ?? '-';
            $status        = $row['status_booking'] ?? '-';
            $createdAt     = $row['created_at'] ?? '-';
            $gambar        = $this->buildGambarUrl($row['gambar'] ?? null);
            $sudahFeedback = !empty($row['sudah_feedback']);

            // Masukkan ke array hasil
            $riwayat[] = [
                'booking_id'     => $bookingId,
                'nama_ruangan'   => $namaRuangan,
                'kode_booking'   => $kodeBooking,
                'tanggal'        => $tanggal,
                'jam'            => $jam,
                'penanggung'     => $penanggung,
                'nim'            => $nim,
                'email'          => $email,
                'nim_ruangan'    => $nimRuangan,
                'status'         => $status,
                'created_at'     => $createdAt,
                'gambar'         => $gambar,
                'sudah_feedback' => $sudahFeedback
            ];
        }
        
        require __DIR__ . '/../views/user/riwayat.php';
    }

    /**
     * Format tanggal dari database ke format Indonesia
     * Contoh: '2024-01-15' menjadi '15 Jan 2024'
     * 
     * @param string|null $tanggal
     * @return string
     */
    private function formatTanggal($tanggal)
    {
        // Cek apakah tanggal ada isinya
        if (empty($tanggal)) {
            return '-';
        }
        
        // Ubah format tanggal
        return date('d M Y', strtotime($tanggal));
    }
    
    /**
     * Gabungkan jam mulai dan jam selesai menjadi rentang waktu
     * Contoh: '09:00:00' dan '11:00:00' menjadi '09:00 - 11:00'
     * 
     * @param string|null $jamMulai
     * @param string|null $jamSelesai
     * @return string
     */
    private function formatRentangJam($jamMulai, $jamSelesai)
    {
        // Format jam mulai (ambil jam dan menit saja)
        $mulai = '';
        if (!empty($jamMulai)) {
            $mulai = date('H:i', strtotime($jamMulai));
        }
        
        // Format jam selesai (ambil jam dan menit saja)
        $selesai = '';
        if (!empty($jamSelesai)) {
            $selesai = date('H:i', strtotime($jamSelesai));
        }
        
        // Gabungkan jam mulai dan selesai
        if ($mulai && $selesai) {
            // Kalau kedua jam ada, gabung dengan ' - '
            $hasil = $mulai . ' - ' . $selesai;
        } elseif ($mulai) {
            // Kalau cuma jam mulai yang ada
            $hasil = $mulai;
        } elseif ($selesai) {
            // Kalau cuma jam selesai yang ada
            $hasil = $selesai;
        } else {
            // Kalau kedua jam kosong
            $hasil = '-';
        }
        
        return $hasil;
    }
    
    /**
     * Build URL lengkap untuk gambar
     * Support URL eksternal (http/https) dan path lokal
     * 
     * @param string|null $gambar
     * @return string
     */
    private function buildGambarUrl($gambar)
    {
        // Kalau gambar kosong, pakai gambar default
        if (empty($gambar)) {
            $gambar = 'public/assets/image/contohruangan.png';
        }
        
        // Cek apakah gambar sudah berupa URL lengkap (http:// atau https://)
        $isUrlLengkap = (strpos($gambar, 'http://') === 0 || strpos($gambar, 'https://') === 0);
        
        if ($isUrlLengkap) {
            // Kalau sudah URL lengkap, langsung return
            return $gambar;
        }
        
        // Kalau path lokal, gabungkan dengan base URL
        $baseUrl = app_config()['base_url'];
        
        // Hapus slash di akhir base URL (kalau ada)
        $baseUrl = rtrim($baseUrl, '/');
        
        // Hapus slash di awal path gambar (kalau ada)
        $gambar = ltrim($gambar, '/');
        
        // Gabungkan base URL dengan path gambar
        return $baseUrl . '/' . $gambar;
    }
}
