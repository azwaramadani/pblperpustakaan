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
        $sql  = "SELECT COUNT(*) AS total FROM {$this->table} WHERE DATE (CREATED_AT) = ?";
        $row  = $this->query($sql, [$date])->fetch();
        return (int)($row['total'] ?? 0);
    }

    // buat dashboard admin hitung semua user yang harus divalidasi atau status akunnya menunggu
    public function mustvalidateRegistered()
    {
        $sql  = "SELECT COUNT(*) AS total FROM {$this->table} WHERE status_akun = 'Menunggu'";
        $row  = $this->query($sql)->fetch();
        return (int)($row['total'] ?? 0);
    }

    // buat dashboard admin hitung semua akun user yang aktif
    public function countAllusers()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        $row = $this->query($sql)->fetch();
        return (int)($row['total']) ?? 0;
    }

    public function usergetAllSortedPaginated(
        string $sortOrder     = 'desc',
        ?string $fromDate     = null,
        ?string $toDate       = null,
        ?string $role         = null,
        ?string $unit         = null,
        ?string $jurusan      = null,
        ?string $programStudi = null,
        ?string $searchName   = null, 
        int $limit            = 10,
        int $page             = 1
    ): array {
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $limit = max(1, $limit);
        $page  = max(1, $page);

        $where  = [];
        $params = [];

        $where[] = "(user.status_akun = 'Disetujui')";

        if (!empty($fromDate)) {
            $where[]  = "user.created_at >= ?";
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where[]  = "user.created_at <= ?";
            $params[] = $toDate;
        }
        if (!empty($role)) {
            $where[]  = "user.role <= ?";
            $params[] = $role;
        }
        if (!empty($unit)) {
            $where[]  = "user.unit = ?";
            $params[] = $unit;
        }
        if (!empty($jurusan)) {
            $where[]  = "user.jurusan = ?";
            $params[] = $jurusan;
        }
        if (!empty($programStudi)) {
            $where[]  = "user.program_studi = ?";
            $params[] = $programStudi;
        }
        if (!empty($searchName)) {
            // Cari di nama atau nim/nip user
            $where[]  = "(user.nim_nip LIKE ? OR user.nama LIKE ?)";
            $like     = '%' . $searchName . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? (" WHERE " . implode(' AND ', $where)) : '';

        // Hitung total baris buat pagination
        $countSql = "SELECT COUNT(*) AS total
                     FROM {$this->table}
                     {$whereSql}";
        $totalRow = $this->query($countSql, $params)->fetch();
        $total    = (int)($totalRow['total'] ?? 0);

        if ($total === 0) {
            return [
                'data'         => [],
                'total'        => 0,
                'page'         => 1,
                'total_pages'  => 1,
                'limit'        => $limit,
            ];
        }

        $totalPages = max(1, (int)ceil($total / $limit));
        if ($page > $totalPages) {
            $page = $totalPages; // clamp supaya ga dapat halaman kosong
        }
        $offset = ($page - 1) * $limit;

        //Ambil data sesuai halaman + urut
        $dataSql = "SELECT *
                    FROM {$this->table}
                    {$whereSql}
                    ORDER BY created_at {$order}
                    LIMIT {$limit} OFFSET {$offset}";
        $rows = $this->query($dataSql, $params)->fetchAll();

        return [
            'data'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'total_pages' => $totalPages,
            'limit'       => $limit,
        ];
    }

    public function userregistgetAllSortedPaginated(
        string $sortOrder     = 'desc',
        ?string $fromDate     = null,
        ?string $toDate       = null,
        ?string $role         = null,
        ?string $unit         = null,
        ?string $jurusan      = null,
        ?string $programStudi = null,
        ?string $statusAkun   = null,
        ?string $searchName   = null, 
        int $limit            = 10,
        int $page             = 1
    ): array {
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        $limit = max(1, $limit);
        $page  = max(1, $page);

        $where  = [];
        $params = [];

        $where[] = "(user.status_akun IN('Menunggu', 'Ditolak'))";

        if (!empty($fromDate)) {
            $where[]  = "user.created_at >= ?";
            $params[] = $fromDate;
        }
        if (!empty($toDate)) {
            $where[]  = "user.created_at <= ?";
            $params[] = $toDate;
        }
        if (!empty($role)) {
            $where[]  = "user.role <= ?";
            $params[] = $role;
        }
        if (!empty($unit)) {
            $where[]  = "user.unit = ?";
            $params[] = $unit;
        }
        if (!empty($jurusan)) {
            $where[]  = "user.jurusan = ?";
            $params[] = $jurusan;
        }
        if (!empty($programStudi)) {
            $where[]  = "user.program_studi = ?";
            $params[] = $programStudi;
        }
        if (!empty($statusAkun)) {
            $where[]  = "user.status_akun = ?";
            $params[] = $statusAkun;
        }
        if (!empty($searchName)) {
            // Cari di nama atau nim/nip user
            $where[]  = "(user.nim_nip LIKE ? OR user.nama LIKE ?)";
            $like     = '%' . $searchName . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = $where ? (" WHERE " . implode(' AND ', $where)) : '';

        // Hitung total baris buat pagination
        $countSql = "SELECT COUNT(*) AS total
                     FROM {$this->table}
                     {$whereSql}";
        $totalRow = $this->query($countSql, $params)->fetch();
        $total    = (int)($totalRow['total'] ?? 0);

        if ($total === 0) {
            return [
                'data'         => [],
                'total'        => 0,
                'page'         => 1,
                'total_pages'  => 1,
                'limit'        => $limit,
            ];
        }

        $totalPages = max(1, (int)ceil($total / $limit));
        if ($page > $totalPages) {
            $page = $totalPages; // clamp supaya ga dapat halaman kosong
        }
        $offset = ($page - 1) * $limit;

        //Ambil data sesuai halaman + urut
        $dataSql = "SELECT *
                    FROM {$this->table}
                    {$whereSql}
                    ORDER BY created_at {$order}
                    LIMIT {$limit} OFFSET {$offset}";
        $rows = $this->query($dataSql, $params)->fetchAll();

        return [
            'data'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'total_pages' => $totalPages,
            'limit'       => $limit,
        ];
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

    # Registrasi user Dosen status akunnya langsung disetujui
    public function registerDosen($data)
    {
        $sql = "INSERT INTO {$this->table}
                (role, nim_nip, jurusan, nama, no_hp, email, password, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Disetujui', NOW())";

        return $this->query($sql, [
            $data['role'] ?? 'Dosen',
            $data['nim_nip'],
            $data['jurusan'],
            $data['nama'],
            $data['no_hp'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    public function registerTendik($data)
    {
        $sql = "INSERT INTO {$this->table}
                (role, nim_nip, unit, nama, no_hp, email, password, status_akun, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Disetujui', NOW())";

        return $this->query($sql, [
            $data['role'] ?? 'Tenaga Kependidikan',
            $data['nim_nip'],
            $data['unit'],
            $data['nama'],
            $data['no_hp'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
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
