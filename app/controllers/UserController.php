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
    
        $userModel = new User();
        $roomModel = new Room();

        $user  = $userModel->findById(Session::get('user_id'));
        $rooms = $roomModel->getAll();

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
                      
        $riwayat = array_map(function ($row) {
            $tanggal = !empty($row['tanggal'])
                ? date('d M Y', strtotime($row['tanggal']))
                : '-';

            $jamMulai   = !empty($row['jam_mulai']) ? date('H:i', strtotime($row['jam_mulai'])) : '';
            $jamSelesai = !empty($row['jam_selesai']) ? date('H:i', strtotime($row['jam_selesai'])) : '';
            $jam        = trim($jamMulai . ($jamMulai && $jamSelesai ? ' - ' : '') . $jamSelesai);

            $gambar    = $row['gambar'] ?: 'public/assets/image/contohruangan.png';
            $gambarUrl = preg_match('#^https?://#i', $gambar)
                ? $gambar
                : rtrim(app_config()['base_url'], '/') . '/' . ltrim($gambar, '/');

            return [
                'booking_id'    => $row['booking_id'],
                'nama_ruangan'  => $row['nama_ruangan'] ?? '-',
                'kode_booking'  => $row['kode_booking'] ?? '-',
                'tanggal'       => $tanggal,
                'jam'           => $jam ?: '-',
                'penanggung'    => $row['nama_penanggung_jawab'] ?? '-',
                'nim'           => $row['nimnip_penanggung_jawab'] ?? '-',
                'email'         => $row['email_penanggung_jawab'] ?? '-',
                'nim_ruangan'   => $row['nimnip_peminjam'] ?? '-',
                'status'        => $row['status_booking'] ?? '-',
                'created_at'    => $row['created_at'] ?? '-',
                'gambar'        => $gambarUrl,
                'sudah_feedback'=> !empty($row['sudah_feedback'])
            ];
        }, $riwayatRaw);

        require __DIR__ . '/../views/user/riwayat.php';
    }
}
