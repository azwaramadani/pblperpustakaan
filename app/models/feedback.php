<?php
# ===============================================
# MODEL: FEEDBACK
# ===============================================
# Menyimpan dan menampilkan feedback user untuk ruangan
# ===============================================

class Feedback extends Model
{
    protected $table = 'feedback';

    # Ambil semua feedback berdasarkan ruangan
    public function getByRoom($room_id)
    {
        $sql = "SELECT f.*, u.nama AS nama_user 
                FROM {$this->table} f
                JOIN user u ON f.user_id = u.user_id
                WHERE f.room_id = ?
                ORDER BY f.tanggal_feedback DESC";
        return $this->query($sql, [$room_id])->fetchAll();
    }


    # buat filter feedback dashboard admin
    public function feedbackgetAllSortedPaginated(
        string $sortOrder     = 'desc',
        ?string $fromDate     = null,
        ?string $toDate       = null,
        ?string $role         = null,
        ?string $unit         = null,
        ?string $jurusan      = null,
        ?string $programStudi = null,
        ?string $feedback     = null,
        ?string $searchName   = null, 
        int $limit            = 10,
        int $page             = 1
    ): array {
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $limit = max(1, $limit);
        $page  = max(1, $page);

        $where  = [];
        $params = [];

        if ($feedback === 'Puas') {
            $where[]  = "f.puas = 1";
        } elseif ($feedback === 'Tidak Puas') {
            $where[]  = "f.puas = 0";
        }
        if (!empty($fromDate)) {
            $where[]  = "f.tanggal_feedback >= ?";
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where[]  = "f.tanggal_feedback <= ?";
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
        if (!empty($searchName)) {
            // Cari berdasarkan nama penanggung jawab (yang buat booking) atau nama user
            $where[]  = "(u.nim_nip LIKE ? OR u.nama LIKE ? OR b.kode_booking ?)";
            $like     = '%' . $searchName . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? (" WHERE " . implode(' AND ', $where)) : '';

        // Hitung total baris buat pagination
        $countSql = "SELECT COUNT(*) AS total
                     FROM {$this->table} f
                     JOIN user u ON f.user_id = u.user_id
                     JOIN room r ON f.room_id = r.room_id
                     JOIN booking b ON f.booking_id = b.booking_id
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

        //Ambil data sesuai halaman + urut
        $dataSql = "SELECT
                    f.feedback_id,
                    f.booking_id,
                    f.user_id,
                    f.room_id,
                    f.puas,
                    f.komentar,
                    f.tanggal_feedback,
                    u.nama AS nama_user,
                    u.nim_nip,
                    r.nama_ruangan,
                    b.kode_booking
                FROM {$this->table} f
                JOIN user u ON f.user_id = u.user_id
                JOIN room r ON f.room_id = r.room_id
                JOIN booking b ON b.booking_id = f.booking_id
                {$whereSql} 
                ORDER BY f.tanggal_feedback {$order}
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
    
    // Ambil feedback berdasarkan booking (untuk form edit/cek sudah ada)
    public function findByBooking($bookingId, $userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE booking_id = ? AND user_id = ? LIMIT 1";
        return $this->query($sql, [$bookingId, $userId])->fetch();
    }

    // Simpan feedback baru
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table}
                (booking_id, user_id, room_id, puas, komentar, tanggal_feedback)
                VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->query($sql, [
            $data['booking_id'],
            $data['user_id'],
            $data['room_id'],
            $data['puas'],
            $data['komentar']
        ]);
    }

    # Hitung rata-rata rating ruangan
    public function puasPercent($roomId): int
    {
        $sql = "SELECT AVG(puas) AS avg_puas FROM {$this->table} WHERE room_id = ?";
        $row = $this->query($sql, [$roomId])->fetch();
        $avg = ($row && $row['avg_puas'] !== null) ? (float)$row['avg_puas'] : 0;
        return (int)round($avg * 100); // hasil 0..100
    }
}
