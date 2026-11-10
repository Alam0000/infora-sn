<?php

class User extends Model
{
    /**
     * Mencari user berdasarkan email.
     */
    public function findByEmail($email)
    {
        return $this->fetchOne(
            "
            SELECT
                id,
                name,
                email,
                password,
                role,
                account_status
            FROM users
            WHERE email = ?
            LIMIT 1
            ",
            "s",
            [$email]
        );
    }

    /**
     * Mencari user berdasarkan ID.
     */
    public function findById($id)
    {
        return $this->fetchOne(
            "
            SELECT
                id,
                name,
                email,
                role,
                account_status
            FROM users
            WHERE id = ?
            LIMIT 1
            ",
            "i",
            [$id]
        );
    }

    /**
     * Membuat user baru.
     */
    public function create($name, $email, $password)
    {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->execute(
            "
            INSERT INTO users
            (
                name,
                email,
                password,
                role,
                account_status,
                created_at,
                updated_at
            )
            VALUES
            (?, ?, ?, 'client', 'active', NOW(), NOW())
            ",
            "sss",
            [
                $name,
                $email,
                $hashedPassword
            ]
        );

        return $this->lastInsertId();
    }

    /**
     * Mengecek apakah email sudah digunakan.
     */
    public function emailExists($email)
    {
        $user = $this->fetchOne(
            "
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
            ",
            "s",
            [$email]
        );

        return $user !== null;
    }
}