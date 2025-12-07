<?php
# ===============================================
# MODEL: ROOM (FINAL, SESUAI STRUKTUR TABEL KAMU)
# ===============================================

class Room extends Model
{
    protected $table = 'room';   

    # Ambil semua ruangan
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY status ASC";
        return $this->query($sql)->fetchAll();
    }

    # method handler buat user pilih ruangan -> booking
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE room_id = ?";
        return $this->query($sql, [$id])->fetch();
    }

    #buat admin dashboard hitung semua ruangan yang tersedia (gaada maintenance misalnya)
    public function countActiveRooms()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE STATUS = 'Tersedia'";
        $row = $this->query($sql)->fetch();
        return (int)($row['total'] ?? 0);
    }

    #buat admin data ruangan
    public function getAllWithStats()
    {
        $sql = "SELECT
                    r.room_id,
                    r.gambar_ruangan,
                    r.nama_ruangan,
                    r.kapasitas_min,
                    r.kapasitas_max,
                    r.deskripsi,
                    r.status,
                    COUNT(DISTINCT b.booking_id) AS total_booking,
                    COUNT(DISTINCT f.feedback_id) AS total_feedback,
                    COALESCE(ROUND(AVG(f.puas) * 100), 0) AS puas_percent
                FROM {$this->table} r
                LEFT JOIN booking b ON b.room_id = r.room_id
                LEFT JOIN feedback f ON f.room_id = r.room_id
                GROUP BY r.room_id, r.gambar_ruangan, r.nama_ruangan, r.kapasitas_min, r.kapasitas_max, r.deskripsi, r.status
                ORDER BY r.created_at ASC";
        return $this->query($sql)->fetchAll();
    }

    # method buat feedback ruangan panel admin
    public function findWithStats(int $id)
    {
        $sql = "SELECT
                    r.room_id,
                    r.gambar_ruangan,
                    r.nama_ruangan,
                    r.kapasitas_min,
                    r.kapasitas_max,
                    r.deskripsi,
                    r.status,
                    COUNT(DISTINCT b.booking_id) AS total_booking,
                    COUNT(DISTINCT f.feedback_id) AS total_feedback,
                    COALESCE(ROUND(AVG(f.puas) * 100), 0) AS puas_percent
                FROM {$this->table} r
                LEFT JOIN booking b ON b.room_id = r.room_id
                LEFT JOIN feedback f ON f.room_id = r.room_id
                WHERE r.room_id = ?
                GROUP BY r.room_id, r.gambar_ruangan, r.nama_ruangan, r.kapasitas_min, r.kapasitas_max, r.deskripsi, r.status
                LIMIT 1";
        return $this->query($sql, [$id])->fetch();
    }

    # Method Tambah ruangan
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table}
                (gambar_ruangan, nama_ruangan, kapasitas_min, kapasitas_max, deskripsi, status)
                VALUES (?, ?, ?, ?, ?, 'Tersedia')";

        return $this->query($sql, [
            $data['gambar_ruangan'],
            $data['nama_ruangan'],
            $data['kapasitas_min'],
            $data['kapasitas_max'],
            $data['deskripsi']
        ]);
    }

    # Method Update data ruangan
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table}
                SET gambar_ruangan=?, nama_ruangan=?, kapasitas_min=?, kapasitas_max=?, deskripsi=?, status=?
                WHERE room_id=?";

        return $this->query($sql, [
            $data['gambar_ruangan'],
            $data['nama_ruangan'],
            $data['kapasitas_min'],
            $data['kapasitas_max'],
            $data['deskripsi'],
            $data['status'],
            $id
        ]);
    }

    # Hapus ruangan
    public function deleteById($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE room_id = ?";
        return $this->query($sql, [$id]);
    }
}
