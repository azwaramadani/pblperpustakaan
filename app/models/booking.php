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
                ORDER BY booking.jam_mulai DESC";
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

    #gethistorybyuser tapi buat cancel booking
    public function cancelgetHistoryByUser($user_id)
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

    # Ubah status booking (Menunggu, Disetujui, Ditolak, Selesai)
    public function updateStatus($booking_id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE booking_id = ?";
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
