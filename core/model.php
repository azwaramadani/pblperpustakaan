<?php
# ===============================================================
# CORE: MODEL
# ===============================================================
# Parent class untuk semua model.
# Menyediakan koneksi PDO dan fungsi query yang siap digunakan.
# ===============================================================

class Model
{
    protected $db;

    public function __construct()
    {
        $cfg = app_config()['database'];

        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";

        $this->db = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    # Eksekusi query (SELECT, INSERT, UPDATE, DELETE)
    protected function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
