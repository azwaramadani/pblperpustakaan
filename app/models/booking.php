<?php
# ===============================================
# MODEL: BOOKING
# ===============================================
# Mengelola proses peminjaman ruangan (user & admin)
# ===============================================

class Booking extends Model
{
    protected $table = 'booking';

    # Ambil semua booking (admin view)
    public function getAll()
    {
        $sql = "SELECT b.*, u.nama AS nama_user, r.nama_ruangan 
                FROM {$this->table} b
                JOIN user u ON b.user_id = u.user_id
                JOIN ruangan r ON b.ruangan_id = r.ruangan_id
                ORDER BY b.created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    # Ambil semua booking milik user
    public function getByUser($user_id)
    {
        $sql = "SELECT b.*, r.nama_ruangan 
                FROM {$this->table} b
                JOIN ruangan r ON b.ruangan_id = r.ruangan_id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        return $this->query($sql, [$user_id])->fetchAll();
    }

    # Detail booking
    public function detail($booking_id)
    {
        $sql = "SELECT b.*, u.email AS email_user, u.nama AS nama_user, r.nama_ruangan 
                FROM {$this->table} b
                JOIN user u ON b.user_id = u.user_id
                JOIN ruangan r ON b.ruangan_id = r.ruangan_id
                WHERE b.booking_id = ?";
        return $this->query($sql, [$booking_id])->fetch();
    }

    # Tambah booking baru
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, ruangan_id, tanggal, jam_mulai, jam_selesai, kode_booking, surat_peminjaman, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Menunggu', NOW())";
        return $this->query($sql, [
            $data['user_id'],
            $data['ruangan_id'],
            $data['tanggal'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['kode_booking'],
            $data['surat_peminjaman'] ?? null
        ]);
    }

    # Ubah status booking (Menunggu, Disetujui, Ditolak, Selesai)
    public function updateStatus($booking_id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE booking_id = ?";
        return $this->query($sql, [$status, $booking_id]);
    }

    # Hapus booking (misalnya jika dibatalkan)
    public function delete($booking_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE booking_id = ?";
        return $this->query($sql, [$booking_id]);
    }

    # Hitung pembatalan hari ini (untuk auto-blokir user)
    public function countCancellationsToday($user_id)
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}
                WHERE user_id = ? AND status = 'Dibatalkan' AND DATE(created_at) = CURDATE()";
        return $this->query($sql, [$user_id])->fetch()['total'];
    }
}
