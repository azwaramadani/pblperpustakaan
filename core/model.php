<?php
# CORE: MODEL
# Parent class buat semua file di folder models
# koneksi PDO dan fungsi query
# ===============================================================
class Model
{
    protected $db;

    // method atau function utama buat koneksi ke database
    public function __construct()
    {
        $cfg = app_config()['database'];

        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";

        $this->db = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    #function utama query buat semua file di folder models
    protected function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
