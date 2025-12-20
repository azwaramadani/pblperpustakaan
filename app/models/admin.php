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
    public function loginAdmin($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        return $this->query($sql, [$username])->fetch();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE admin_id = ?";
        return $this->query($sql, [$id])->fetch();
    }
}
