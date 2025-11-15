<?php
# ===============================================
# MODEL: ADMIN
# ===============================================
# Menangani operasi data admin (login, validasi akun user, dsb)
# ===============================================

class Admin extends Model
{
    protected $table = 'admin';

    # Login admin
    public function login($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        return $this->query($sql, [$username])->fetch();
    }

    # Ambil semua user yang belum divalidasi
    public function getPendingUsers()
    {
        $sql = "SELECT * FROM user WHERE status_akun = 'Menunggu'";
        return $this->query($sql)->fetchAll();
    }

    # Setujui akun user
    public function approveUser($user_id)
    {
        $sql = "UPDATE user SET status_akun = 'Disetujui' WHERE user_id = ?";
        return $this->query($sql, [$user_id]);
    }

    # Tolak akun user
    public function rejectUser($user_id)
    {
        $sql = "UPDATE user SET status_akun = 'Ditolak' WHERE user_id = ?";
        return $this->query($sql, [$user_id]);
    }
}
