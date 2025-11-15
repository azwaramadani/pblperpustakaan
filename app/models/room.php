<?php
# ===============================================
# MODEL: ROOM
# ===============================================
# Mengatur data ruangan (CRUD, status, rating)
# ===============================================

class Room extends Model
{
    protected $table = 'ruangan';

    # Ambil semua ruangan
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nama_ruangan ASC";
        return $this->query($sql)->fetchAll();
    }

    # Ambil ruangan populer (misalnya 3 teratas)
    public function getPopular()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY rating DESC LIMIT 3";
        return $this->query($sql)->fetchAll();
    }

    # Ambil ruangan berdasarkan ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE ruangan_id = ?";
        return $this->query($sql, [$id])->fetch();
    }

    # Tambah ruangan baru
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (nama_ruangan, gambar_ruangan, kapasitas_min, kapasitas_max, deskripsi, status)
                VALUES (?, ?, ?, ?, ?, 'Tersedia')";
        return $this->query($sql, [
            $data['nama_ruangan'],
            $data['gambar_ruangan'],
            $data['kapasitas_min'],
            $data['kapasitas_max'],
            $data['deskripsi']
        ]);
    }

    # Ubah data ruangan
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET nama_ruangan=?, gambar_ruangan=?, kapasitas_min=?, kapasitas_max=?, deskripsi=? WHERE ruangan_id=?";
        return $this->query($sql, [
            $data['nama_ruangan'],
            $data['gambar_ruangan'],
            $data['kapasitas_min'],
            $data['kapasitas_max'],
            $data['deskripsi'],
            $id
        ]);
    }

    # Ubah status ruangan (Tersedia / Tidak Tersedia)
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE ruangan_id = ?";
        return $this->query($sql, [$status, $id]);
    }
}
