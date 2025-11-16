<?php
# ===============================================
# MODEL: USER (FINAL VERSION)
# ===============================================

class User extends Model
{
    protected $table = 'user';

    # ==========================================================
    # Ambil semua user (untuk admin)
    # ==========================================================
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    # ==========================================================
    # Cari user berdasarkan ID
    # ==========================================================
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        return $this->query($sql, [$id])->fetch();
    }

    # ==========================================================
    # Cari user by NIM/NIP → untuk login
    # ==========================================================
    public function findByNIMNIP($nim_nip)
    {
        $sql = "SELECT * FROM {$this->table} WHERE nim_nip = ? LIMIT 1";
        return $this->query($sql, [$nim_nip])->fetch();
    }

    # ==========================================================
    # Cari user by email (opsional)
    # Bisa dipakai untuk validasi duplikasi email
    # ==========================================================
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->query($sql, [$email])->fetch();
    }

    # ==========================================================
    # Registrasi user mahasiswa
    #
    # Catatan:
    # - mahasiswa: wajib upload bukti aktivasi Kubaca
    # - dosen/tendik: tidak wajib → nanti buat method terpisah
    # ==========================================================
    public function registerMahasiswa($data)
    {
        $sql = "INSERT INTO {$this->table}
                (nim_nip, nama, no_hp, email, password, bukti_aktivasi, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'Menunggu', NOW())";

        return $this->query($sql, [
            $data['nim_nip'],
            $data['nama'],
            $data['no_hp'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['bukti_aktivasi']
        ]);
    }

    # ==========================================================
    # Registrasi user Dosen / Tendik
    # (tanpa upload bukti aktivasi)
    # ==========================================================
    public function registerDosenOrTendik($data)
    {
        $sql = "INSERT INTO {$this->table}
                (nim_nip, nama, no_hp, email, password, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, 'Disetujui', NOW())";

        return $this->query($sql, [
            $data['nim_nip'],
            $data['nama'],
            $data['no_hp'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    # ==========================================================
    # Update status akun
    # ==========================================================
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status_akun = ? WHERE user_id = ?";
        return $this->query($sql, [$status, $id]);
    }

    # ==========================================================
    # Blokir user otomatis jika batalkan booking 2x
    # ==========================================================
    public function blockUser($id)
    {
        $sql = "UPDATE {$this->table} SET status_akun = 'Diblokir' WHERE user_id = ?";
        return $this->query($sql, [$id]);
    }
}
