<?php

class AdminClient extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Ambil semua client
    |--------------------------------------------------------------------------
    */

    public function getAllClients($keyword = '', $status = '')
    {
        $sql = "
            SELECT
                id,
                name,
                email,
                role,
                account_status,
                created_at,
                updated_at
            FROM users
            WHERE role = 'client'
        ";

        $params = [];
        $types = '';

        if ($keyword !== '') {

            $sql .= "
                AND (
                    name LIKE ?
                    OR email LIKE ?
                )
            ";

            $search = '%' . $keyword . '%';

            $params[] = $search;
            $params[] = $search;

            $types .= 'ss';
        }

        if ($status !== '') {

            $sql .= "
                AND account_status = ?
            ";

            $params[] = $status;

            $types .= 's';
        }

        $sql .= "
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param(
                $types,
                ...$params
            );
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Statistik client
    |--------------------------------------------------------------------------
    */

    public function getStats()
    {
        $stats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0
        ];


        $result = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(
                    account_status = 'active'
                ) AS active,
                SUM(
                    account_status = 'inactive'
                ) AS inactive
            FROM users
            WHERE role = 'client'
        ");


        if ($result) {

            $row = $result->fetch_assoc();

            $stats['total'] =
                (int) ($row['total'] ?? 0);

            $stats['active'] =
                (int) ($row['active'] ?? 0);

            $stats['inactive'] =
                (int) ($row['inactive'] ?? 0);
        }

        return $stats;
    }


    /*
    |--------------------------------------------------------------------------
    | Detail client
    |--------------------------------------------------------------------------
    */

    public function getClientById($id)
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                name,
                email,
                role,
                account_status,
                created_at,
                updated_at
            FROM users
            WHERE id = ?
              AND role = 'client'
            LIMIT 1
        ");

        $stmt->bind_param(
            'i',
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }


    /*
    |--------------------------------------------------------------------------
    | Tambah client
    |--------------------------------------------------------------------------
    */

    public function createClient(
        $name,
        $email,
        $password,
        $status = 'active'
    ) {
        $hashedPassword =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );

        $stmt = $this->db->prepare("
            INSERT INTO users
            (
                name,
                email,
                password,
                role,
                account_status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'client',
                ?
            )
        ");

        $stmt->bind_param(
            'ssss',
            $name,
            $email,
            $hashedPassword,
            $status
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | Cek email
    |--------------------------------------------------------------------------
    */

    public function emailExists(
        $email,
        $excludeId = 0
    ) {
        $stmt = $this->db->prepare("
            SELECT id
            FROM users
            WHERE email = ?
              AND id != ?
            LIMIT 1
        ");

        $stmt->bind_param(
            'si',
            $email,
            $excludeId
        );

        $stmt->execute();

        return $stmt
            ->get_result()
            ->num_rows > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Update client
    |--------------------------------------------------------------------------
    */

    public function updateClient(
        $id,
        $name,
        $email,
        $status
    ) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                name = ?,
                email = ?,
                account_status = ?
            WHERE id = ?
              AND role = 'client'
        ");

        $stmt->bind_param(
            'sssi',
            $name,
            $email,
            $status,
            $id
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | Update password
    |--------------------------------------------------------------------------
    */

    public function updatePassword(
        $id,
        $password
    ) {
        $hashedPassword =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );

        $stmt = $this->db->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
              AND role = 'client'
        ");

        $stmt->bind_param(
            'si',
            $hashedPassword,
            $id
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | Hapus client
    |--------------------------------------------------------------------------
    */

    public function deleteClient($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM users
            WHERE id = ?
              AND role = 'client'
        ");

        $stmt->bind_param(
            'i',
            $id
        );

        return $stmt->execute();
    }
}