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
