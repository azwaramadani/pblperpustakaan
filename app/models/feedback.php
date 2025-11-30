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
    public function getAllWithFilters(string $sortDate = 'desc', string $feedbackFilter = 'all')
    {
        // Amankan nilai sort (ASC/DESC)
        $order = strtoupper($sortDate) === 'ASC' ? 'ASC' : 'DESC';

        $where  = [];
        $params = [];

        // Filter puas: 'puas' -> puas=1, 'tidak' -> puas=0
        if ($feedbackFilter === 'puas') {
            $where[]  = "f.puas = 1";
        } elseif ($feedbackFilter === 'tidak') {
            $where[]  = "f.puas = 0";
        }

        $sql = "SELECT
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
                LEFT JOIN booking b ON b.booking_id = f.booking_id";

        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        // Urutkan berdasarkan tanggal_feedback
        $sql .= " ORDER BY f.tanggal_feedback {$order}";

        return $this->query($sql, $params)->fetchAll();
    }

    // Versi lama (tanpa filter) masih bisa dipakai jika diperlukan
    public function getAllWithRelations()
    {
        return $this->getAllWithFilters('desc', 'all');
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
