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
        $sql = "SELECT booking.*, u.nama AS nama_user, r.nama_ruangan 
                FROM {$this->table} booking
                JOIN user u ON booking.user_id = u.user_id
                JOIN room r ON booking.room_id = r.room_id
                ORDER BY booking.waktu_booking DESC";
        return $this->query($sql)->fetchAll();
    }

    # Ambil riwayat booking user lengkap dengan info ruangan & feedback
    public function getHistoryByUser($user_id)
    {
        $sql = "SELECT
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
                JOIN room r ON b.room_id = r.room_id
                LEFT JOIN feedback f ON f.booking_id = b.booking_id
                WHERE b.user_id = ?
                ORDER BY b.jam_mulai DESC";
        return $this->query($sql, [$user_id])->fetchAll();
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
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, room_id, tanggal, jam_mulai, jam_selesai, jumlah_mahasiswa, nama_penanggung_jawab, nimnip_penanggung_jawab
                , email_penanggung_jawab, nimnip_peminjam, kode_booking, waktu_booking, status_booking)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?  NOW(), 'Menunggu')";
        return $this->query($sql, [
            $data['user_id'],
            $data['room_id'],
            $data['tanggal'],
            $data['jam_mulai'],
            $data['jam_selesai'],
            $data['jumlah_mahasiswa'],
            $data['nama_penanggung_jawab'],
            $data['nimnim_penanggung_jawab'],
            $data['email_penanggung_jawab'],
            $data['nimnip_peminjam'],
            $data['waktu_booking'],
            $data['kode_booking'],
            $data['status_booking']
        ]);
    }

    # crete booking buat ruang rapat
    public function createbookingrapat($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, room_id, tanggal, jam_mulai, jam_selesai, nama_penanggung_jawab, surat_peminjaman_ruang_rapat,
                kode_booking, waktu_booking, status_booking)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Menunggu')";
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
                WHERE user_id = ? AND status = 'Dibatalkan' AND DATE(waktu_booking) = CURDATE()";
        return $this->query($sql, [$user_id])->fetch()['total'];
    }
}
