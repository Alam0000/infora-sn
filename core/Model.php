<?php

require_once __DIR__ . '/../config/database.php';

class Model
{
    protected $db;

    public function __construct()
    {
        global $koneksi;

        $this->db = $koneksi;
    }

    /**
     * Menjalankan query SELECT.
     *
     * @param string $sql
     * @return mysqli_result
     */
    protected function query($sql)
    {
        return $this->db->query($sql);
    }

    /**
     * Menjalankan prepared statement.
     *
     * @param string $sql
     * @param string $types
     * @param array $params
     * @return mysqli_stmt
     */
    protected function execute($sql, $types = '', $params = [])
    {
        $stmt = $this->db->prepare($sql);

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        return $stmt;
    }

    /**
     * Mengambil satu data.
     *
     * @param string $sql
     * @param string $types
     * @param array $params
     * @return array|null
     */
    protected function fetchOne($sql, $types = '', $params = [])
    {
        $stmt = $this->execute($sql, $types, $params);

        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }

    /**
     * Mengambil banyak data.
     *
     * @param string $sql
     * @param string $types
     * @param array $params
     * @return array
     */
    protected function fetchAll($sql, $types = '', $params = [])
    {
        $stmt = $this->execute($sql, $types, $params);

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mendapatkan ID data terakhir.
     *
     * @return int
     */
    protected function lastInsertId()
    {
        return $this->db->insert_id;
    }

    /**
     * Mendapatkan jumlah baris yang terpengaruh.
     *
     * @return int
     */
    protected function affectedRows()
    {
        return $this->db->affected_rows;
    }
}