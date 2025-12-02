<?php
# ===============================================
# MODEL: USER (FINAL VERSION)
# ===============================================

class User extends Model
{
    protected $table = 'user';

    // buat dashboard admin hitung semua user bikin akun baru hari ini
    public function countRegisteredToday()
    {
        $date = $date ?? date('Y-m-d');
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE DATE (CREATED_AT) = ?";
        $row = $this->query($sql, [$date])->fetch();
        return (int)($row['total'] ?? 0);
    }

    // buat dashboard admin hitung semua akun user yang aktif
    public function countAllusers()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $row = $this->query($sql)->fetch();
        return (int)($row['total']) ?? 0;
    }

    public function userMenungguandDitolak()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status_akun IN('Ditolak', 'Menunggu') ORDER BY created_at DESC";
    }

    // buat admin data akun user dengan urutan akun dibuat terbaru
    public function usergetAllOrdered()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status_akun = 'Disetujui' ORDER BY created_at DESC";
        return $this->query($sql)->fetchAll();
    }

    //method hapus akun user di page admin data akun
    public function deleteById($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        return $this->query($sql, [$id]);
    }

    # Cari user berdasarkan ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        return $this->query($sql, [$id])->fetch();
    }

    # Cari user by NIM/NIP buat login
    public function findByNIMNIP($nim_nip)
    {
        $sql = "SELECT * FROM {$this->table} WHERE nim_nip = ? LIMIT 1";
        return $this->query($sql, [$nim_nip])->fetch();
    }
    
    # cari user by email Bisa dipake buat validasi duplikasi email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->query($sql, [$email])->fetch();
    }

    public function isNIMExists($nim_nip): bool
    {
        $sql = "SELECT user_id FROM {$this->table} WHERE nim_nip = ? LIMIT 1";
        return (bool)$this->query($sql, [$nim_nip])->fetch();
    }

    public function isEmailExists($email): bool
    {
        $sql = "SELECT user_id FROM {$this->table} WHERE email = ? LIMIT 1";
        return (bool)$this->query($sql, [$email])->fetch();
    }

    # Registrasi user mahasiswa
    public function registerMahasiswa($data)
    {
        $sql = "INSERT INTO {$this->table}
                (nim_nip, jurusan, program_studi, nama, no_hp, email, password, role, bukti_aktivasi, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu', NOW())";

        return $this->query($sql, [
            $data['nim_nip'],
            $data['jurusan'],
            $data['program_studi'],
            $data['nama'],
            $data['no_hp'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'Mahasiswa',
            $data['bukti_aktivasi']
        ]);
    }

    # Registrasi user Dosen / Tendik, status akunnya langsung disetujui
    public function registerDosenOrTendik($data)
    {
        $sql = "INSERT INTO {$this->table}
                (nim_nip, jurusan, nama, no_hp, email, password, role, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Disetujui', NOW())";

        return $this->query($sql, [
            $data['nim_nip'],
            $data['jurusan'],
            $data['nama'],
            $data['no_hp'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'Dosen/Tendik'
        ]);
    }

    # Update status akun
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status_akun = ? WHERE user_id = ?";
        return $this->query($sql, [$status, $id]);
    }


    # Blokir user otomatis jika batalkan booking 2x
    public function blockUser($id)
    {
        $sql = "UPDATE {$this->table} SET status_akun = 'Diblokir' WHERE user_id = ?";
        return $this->query($sql, [$id]);
    }
}
