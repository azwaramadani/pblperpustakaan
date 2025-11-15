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
    public function getByRoom($ruangan_id)
    {
        $sql = "SELECT f.*, u.nama AS nama_user 
                FROM {$this->table} f
                JOIN user u ON f.user_id = u.user_id
                WHERE f.ruangan_id = ?
                ORDER BY f.created_at DESC";
        return $this->query($sql, [$ruangan_id])->fetchAll();
    }

    # Tambah feedback
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, ruangan_id, rating, komentar, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        return $this->query($sql, [
            $data['user_id'],
            $data['ruangan_id'],
            $data['rating'],
            $data['komentar']
        ]);
    }

    # Hitung rata-rata rating ruangan
    public function averageRating($ruangan_id)
    {
        $sql = "SELECT AVG(rating) AS avg_rating FROM {$this->table} WHERE ruangan_id = ?";
        $row = $this->query($sql, [$ruangan_id])->fetch();
        return $row ? round($row['avg_rating'], 1) : 0;
    }
}
