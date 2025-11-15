<?php
# ===============================================
# MODEL: USER
# ===============================================
# Berisi semua operasi database yang berhubungan
# dengan akun user (register, login, validasi, dsb)
# ===============================================

class User extends Model
{
    protected $table = 'user';

    # Ambil semua user (untuk halaman admin data akun)
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    # Cari user berdasarkan ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        return $this->query($sql, [$id])->fetch();
    }

    # Cari user berdasarkan email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        return $this->query($sql, [$email])->fetch();
    }

    # Registrasi user baru
    public function register($data)
    {
        $sql = "INSERT INTO {$this->table} (nama, email, password, no_hp, nim, bukti_aktivasi, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'Menunggu', NOW())";
        return $this->query($sql, [
            $data['nama'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['no_hp'],
            $data['nim'],
            $data['bukti_aktivasi']
        ]);
    }

    # Login user
    public function login($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND status_akun = 'Disetujui'";
        return $this->query($sql, [$email])->fetch();
    }

    # Ubah status akun (Menunggu, Disetujui, Ditolak, Diblokir)
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status_akun = ? WHERE user_id = ?";
        return $this->query($sql, [$status, $id]);
    }

    # Blokir user (jika melanggar batas pembatalan)
    public function blockUser($id)
    {
        $sql = "UPDATE {$this->table} SET status_akun = 'Diblokir' WHERE user_id = ?";
        return $this->query($sql, [$id]);
    }
}
