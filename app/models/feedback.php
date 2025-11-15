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

    # Tambah feedback
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (booking_id, room_id, user_id, puas, komentar, tanggal_feedback)
                VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->query($sql, [
            $data['booking_id'],
            $data['room_id'],
            $data['user_id'],
            $data['puas'],
            $data['komentar'] ?? NULL,
            $data['tanggal_feedback']
        ]);
    }

    # Hitung rata-rata rating ruangan
    public function averageRating($room_id)
    {
        $sql = "SELECT AVG(puas) AS Puas FROM {$this->table} WHERE room_id = ?";
        $row = $this->query($sql, [$room_id])->fetch();
        return $row ? round($row['Puas'], 1) : 0;
    }
}
