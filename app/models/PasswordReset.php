<?php

class PasswordReset extends Model {
    protected $table = "password_resets";

    public function createToken($user_id, $token, $expired_at)
    {
        $sql = "INSERT INTO {$this->table} (user_id, token, expired_at) VALUES (?, ?, ?)";
        return $this->query($sql, [$user_id, $token, $expired_at]);
    }

    public function findValidToken($token)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE token = ?
                AND used = 0
                AND expired_at > NOW()
                LIMIT 1";

        return $this->query($sql, [$token])->fetch();
    }

    public function markUsed($id)
    {
        $sql = "UPDATE {$this->table} SET used = 1 WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}

?>