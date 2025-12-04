<?php
# MODEL: BOOKING
# class inheritance dari class core/Model
#===============================================================

class Booking extends Model
{
    protected $table = 'booking';

    #ini jg method buat data book dashboard admin, tp buat backup aja kalo filtering/sorting error
    public function getAll()
    {
        $sql = "SELECT b.*, u.role, u.jurusan, u.program_studi, u.nama AS nama_user, 
                u.nim_nip, r.nama_ruangan
                FROM {$this->table} b
                JOIN user u ON b.user_id = u.user_id
                JOIN room r ON b.room_id = r.room_id
                ORDER BY b.tanggal DESC, b.jam_mulai DESC";
        return $this->query($sql)->fetchAll();
    }

    # method data booking buat admin, pake sorting dan pagination
    public function getAllSortedPaginated(
        string $sortOrder     = 'desc',
        ?string $fromDate     = null,
        ?string $toDate       = null,
        ?string $role         = null,
        ?string $unit         = null,
        ?string $jurusan      = null,
        ?string $programStudi = null,
        int $limit            = 10,
        int $page             = 1
    ): array {
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $limit = max(1, $limit);
        $page  = max(1, $page);

        $where  = [];
        $params = [];

        if (!empty($fromDate)) {
            $where[]  = "b.tanggal >= ?";
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where[]  = "b.tanggal <= ?";
            $params[] = $toDate;
        }
        if (!empty($role)) {
            $where[]  = "u.role <= ?";
            $params[] = $role;
        }
        if (!empty($unit)) {
            $where[]  = "u.unit = ?";
            $params[] = $unit;
        }
        if (!empty($jurusan)) {
            $where[]  = "u.jurusan = ?";
            $params[] = $jurusan;
        }
        if (!empty($programStudi)) {
            $where[]  = "u.program_studi = ?";
            $params[] = $programStudi;
        }

        $whereSql = $where ? (" WHERE " . implode(' AND ', $where)) : '';

        // Hitung total baris supaya tahu total halaman
        $countSql = "SELECT COUNT(*) AS total
                     FROM {$this->table} b
                     JOIN user u ON b.user_id = u.user_id
                     JOIN room r ON b.room_id = r.room_id
                     {$whereSql}";
        $totalRow = $this->query($countSql, $params)->fetch();
        $total    = (int)($totalRow['total'] ?? 0);

        if ($total === 0) {
            return [
                'data'         => [],
                'total'        => 0,
                'page'         => 1,
                'total_pages'  => 1,
                'limit'        => $limit,
            ];
        }

        $totalPages = max(1, (int)ceil($total / $limit));
        if ($page > $totalPages) {
            $page = $totalPages; // clamp supaya ga dapat halaman kosong
        }
        $offset = ($page - 1) * $limit;

        // Ambil data sesuai halaman
        $dataSql = "SELECT
                        b.*,
                        u.role,
                        u.unit,
                        u.jurusan,
                        u.program_studi,
                        u.nama AS nama_user,
                        u.nim_nip,
                        r.nama_ruangan
                    FROM {$this->table} b
                    JOIN user u ON b.user_id = u.user_id
                    JOIN room r ON b.room_id = r.room_id
                    {$whereSql}
                    ORDER BY b.tanggal {$order}, b.jam_mulai {$order}
                    LIMIT {$limit} OFFSET {$offset}";
        $rows = $this->query($dataSql, $params)->fetchAll();

        return [
            'data'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'total_pages' => $totalPages,
            'limit'       => $limit,
        ];
    }

    public function getAllSortedPaginatedToday(
        string $sortOrder     = 'desc',
        ?string $role         = null,
        ?string $unit         = null,
        ?string $jurusan      = null,
        ?string $programStudi = null,
        int $limit            = 10,
        int $page             = 1
    ): array {
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $limit = max(1, $limit);
        $page  = max(1, $page);

        $where  = [];
        $params = [];

        if (!empty($role)) {
            $where[]  = "u.role <= ?";
            $params[] = $role;
        }
        if (!empty($unit)) {
            $where[]  = "u.unit <= ?";
            $params[] = $unit;
        }
        if (!empty($jurusan)) {
            $where[]  = "u.jurusan = ?";
            $params[] = $jurusan;
        }
        if (!empty($programStudi)) {
            $where[]  = "u.program_studi = ?";
            $params[] = $programStudi;
        }

        $whereSql = $where ? (" WHERE " . implode(' AND ', $where)) : '';

        // Hitung total baris supaya tahu total halaman
        $countSql = "SELECT COUNT(*) AS total
                     FROM {$this->table} b
                     JOIN user u ON b.user_id = u.user_id
                     JOIN room r ON b.room_id = r.room_id
                     {$whereSql}";
        $totalRow = $this->query($countSql, $params)->fetch();
        $total    = (int)($totalRow['total'] ?? 0);

        if ($total === 0) {
            return [
                'data'         => [],
                'total'        => 0,
                'page'         => 1,
                'total_pages'  => 1,
                'limit'        => $limit,
            ];
        }

        $totalPages = max(1, (int)ceil($total / $limit));
        if ($page > $totalPages) {
            $page = $totalPages; // clamp supaya ga dapat halaman kosong
        }
        $offset = ($page - 1) * $limit;

        // Ambil data sesuai halaman
        $dataSql = "SELECT
                    b.booking_id,
                    b.kode_booking,
                    b.tanggal,
                    b.jam_mulai,
                    b.jam_selesai,
                    b.created_at,
                    b.status_booking,
                    b.jumlah_peminjam,
                    b.nimnip_penanggung_jawab,
                    u.role,
                    u.unit,
                    u.jurusan,
                    u.program_studi,
                    u.nama AS nama_penanggung_jawab,
                    u.nim_nip,
                    r.nama_ruangan,
                    COALESCE(b.jumlah_peminjam, COUNT(b.nimnip_peminjam)) AS total_peminjam
                FROM {$this->table} b
                JOIN user u ON b.user_id = u.user_id
                JOIN room r ON b.room_id = r.room_id
                {$whereSql}
                GROUP BY b.booking_id
                ORDER BY b.tanggal {$order}, b.jam_mulai {$order}
                LIMIT {$limit} OFFSET {$offset}";
        $rows = $this->query($dataSql, $params)->fetchAll();

        return [
            'data'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'total_pages' => $totalPages,
            'limit'       => $limit,
        ];
    }

    #buat admin dashboard hitung semua bookingan hari ini
    public function countBookingToday()
    {
        $date = $date ?? date('Y-m-d');
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE tanggal = ?";
        $row = $this->query($sql, [$date])->fetch();
        return (int)($row['total'] ?? 0);
    }

    # Query buat nampilin urutan ruangan terbanyak yang dibooking sama user/admin buat admin dashboard atau bisa juga home/index
    public function getTopRoomsByBooking(int $limit = 9)
    {
        $limit = (int) $limit;
        $sql = "SELECT
                    r.room_id,
                    r.nama_ruangan,
                    r.kapasitas_min,
                    r.kapasitas_max,
                    r.status,
                    COUNT(b.booking_id) AS total_booking
                FROM room r
                LEFT JOIN {$this->table} b ON b.room_id = r.room_id
                GROUP BY r.room_id, r.nama_ruangan, r.kapasitas_min, r.kapasitas_max, r.status
                ORDER BY total_booking DESC, r.nama_ruangan ASC
                LIMIT {$limit}";
        return $this->query($sql)->fetchAll();
    }

    #buat admin data booking di hari ini atau hari sistem
    public function getBookingsByDate(string $date)
    {
        $sql = "SELECT
                    b.booking_id,
                    b.kode_booking,
                    b.tanggal,
                    b.jam_mulai,
                    b.jam_selesai,
                    b.created_at,
                    b.status_booking,
                    b.jumlah_peminjam,
                    COUNT(b.nimnip_peminjam) AS total_peminjam,
                    u.role,
                    u.jurusan,
                    u.program_studi,
                    u.nama AS nama_penanggung_jawab,
                    u.nim_nip AS nim_nip_penanggung_jawab
                FROM {$this->table} b
                JOIN user u ON b.user_id = u.user_id
                WHERE b.tanggal = ?
                GROUP BY
                    b.booking_id,
                    b.kode_booking,
                    b.tanggal,
                    b.jam_mulai,
                    b.jam_selesai,
                    b.created_at,
                    b.status_booking,
                    b.jumlah_peminjam,
                    u.role,
                    u.jurusan,
                    u.program_studi,
                    u.nama,
                    u.nim_nip
                ORDER BY b.created_at DESC";

        $rows = $this->query($sql, [$date])->fetchAll();

        // Jika jumlah_peminjam sudah diisi, pakai itu; kalau belum, pakai hasil COUNT()
        foreach ($rows as &$row) {
            $row['total_peminjam'] = $row['jumlah_peminjam'] !== null
                ? (int)$row['jumlah_peminjam']
                : (int)$row['total_peminjam'];
        }
        unset($row);

        return $rows;
    }

    # Ambil riwayat booking per user
    public function getHistoryByUser($user_id)
    {
        $sql = "SELECT
                    u.nama,
                    b.booking_id,
                    b.kode_booking,
                    b.tanggal,
                    b.jam_mulai,
                    b.jam_selesai,
                    b.nama_penanggung_jawab,
                    b.nimnip_penanggung_jawab,
                    b.email_penanggung_jawab,
                    b.nimnip_peminjam,
                    b.status_booking,
                    r.nama_ruangan,
                    r.gambar_ruangan AS gambar,
                    CASE WHEN f.booking_id IS NULL THEN 0 ELSE 1 END AS sudah_feedback
                FROM {$this->table} b
                JOIN user u ON b.user_id = u.user_id
                JOIN room r ON b.room_id = r.room_id
                LEFT JOIN feedback f ON f.booking_id = b.booking_id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        return $this->query($sql, [$user_id])->fetchAll();
    }

    #buat cancel booking, bisa juga dipake buat feedback 
    public function findByIdAndUser($bookingId, $userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE booking_id = ? AND user_id = ? LIMIT 1";
        return $this->query($sql, [$bookingId, $userId])->fetch();
    }

    # Detail booking
    public function detail($booking_id)
    {
        $sql = "SELECT booking.*, u.email AS email_user, u.nama AS nama_user, r.nama_ruangan 
                FROM {$this->table} booking
                JOIN user u ON booking.user_id = u.user_id
                JOIN room r ON b.room_id = r.room_id
                WHERE booking.booking_id = ?";
        return $this->query($sql, [$booking_id])->fetch();
    }

    # Tambah booking baru
    public function createUserBooking($data)
    {
        $sql = "INSERT INTO {$this->table}
                (user_id, room_id, tanggal, jam_mulai, jam_selesai, jumlah_peminjam,
                 nama_penanggung_jawab, nimnip_penanggung_jawab, email_penanggung_jawab,
                 nimnip_peminjam, kode_booking, status_booking)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['user_id'],
            $data['room_id'],
            $data['tanggal'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['jumlah_peminjam'],
            $data['nama_penanggung_jawab'],
            $data['nimnip_penanggung_jawab'],
            $data['email_penanggung_jawab'],
            $data['nimnip_peminjam'],
            $data['kode_booking'],
            $data['status_booking']
        ]);
    }


        // Cek bentrok booking lain di ruangan dan tanggal yang sama
    public function hasOverlap($room_id, $tanggal, $jam_mulai, $jam_selesai, $exclude_booking_id = null)
    {
        $sql = "SELECT COUNT(*) AS cnt
                FROM {$this->table}
                WHERE room_id = ?
                  AND status_booking IN ('Disetujui')
                  AND tanggal = ?
                  AND NOT (jam_selesai <= ? OR jam_mulai >= ?)";
        $params = [$room_id, $tanggal, $jam_mulai, $jam_selesai];

        if ($exclude_booking_id) {
            $sql .= " AND booking_id <> ?";
            $params[] = $exclude_booking_id;
        }

        $row = $this->query($sql, $params)->fetch();
        return ($row['cnt'] ?? 0) > 0;
    }

    # Cek apakah NIM/NIP peminjam sudah pernah booking ruangan ini pada tanggal yang sama
    public function memberAlreadyBooked($nimnip, $room_id, $tanggal)
    {
        $sql = "SELECT COUNT(*) AS cnt
                FROM {$this->table}
                WHERE nimnip_peminjam = ?
                AND room_id = ?
                AND tanggal = ?
                AND status_booking IN ('Disetujui', 'Selesai')";

        $row = $this->query($sql, [$nimnip, $room_id, $tanggal])->fetch();
        return ($row['cnt'] ?? 0) > 0;
    }

    # crete booking buat ruang rapat
    public function createbookingrapat($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, room_id, tanggal, jam_mulai, jam_selesai, nama_penanggung_jawab, surat_peminjaman_ruang_rapat,
                kode_booking, waktu_booking, status_booking)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Disetujui')";
        return $this->query($sql, [
            $data['user_id'],
            $data['ruangan_id'],
            $data['tanggal'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['nama_penanggung_jawab'],
            $data['surat_peminjaman_ruang_rapat'] ?? null,
            $data['kode_booking']
        ]);
    }

    #auto update status selesai
    public function markFinishedBookings()
    {
        $sql = "UPDATE {$this->table}
                SET status_booking = 'Selesai'
                WHERE status_booking = 'Disetujui'
                AND (
                        tanggal < CURDATE()
                        OR (tanggal = CURDATE() AND jam_selesai <= CURTIME())
                    )";
        return $this->query($sql);
    }

    # Ubah status booking (Disetujui, Ditolak, Selesai)
    public function updateStatus($booking_id, $status)
    {
        $sql = "UPDATE {$this->table} SET status_booking = ? WHERE booking_id = ?";
        return $this->query($sql, [$status, $booking_id]);
    }

    #kalo booking dibatalin
    public function cancelByUser($booking_id, $user_id)
    {
        $sql = "UPDATE {$this->table}
                SET status_booking = 'Dibatalkan'
                WHERE booking_id = ? AND user_id = ? AND status_booking = 'Disetujui'";
        return $this->query($sql, [$booking_id, $user_id]);
    }


    # Hitung pembatalan hari ini (untuk auto-blokir user)
    public function countCancellationsToday($user_id)
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}
                WHERE user_id = ? AND status = 'Dibatalkan' AND DATE(waktu_booking) = CURDATE()";
        return $this->query($sql, [$user_id])->fetch()['total'];
    }
}
